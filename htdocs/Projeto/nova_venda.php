<?php

session_name('mercado');
session_start();

include "includes/cabecalho.php";
include "includes/conect.php";

$query_venda = "SELECT MAX(id) AS id FROM vendas";

$result_venda = mysqli_query($conect, $query_venda);


$dados_venda = mysqli_fetch_array($result_venda);

$id_venda = $dados_venda['id'] + 1;



$query = "SELECT id,nome,marca,preco_venda,embalagem,unidade,cod_barras, estoque FROM produtos";

$sql_query_produtos = mysqli_query($conect, $query);
$num_result = mysqli_num_rows($sql_query_produtos);
?>

<main>
    <div class="container-fluid">

        <div class="col-md-12 listagem-produtos">
            <h1 class="mt-4">Nova Venda</h1>
            <br>
            <div class="card">
                <div class="card-body">

                    <form name="nova_venda">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Funcion&aacute;rio respons&aacute;vel pela venda</label>
                                <select class="form-select" name="funcionario" id="funcionario">
                                    <option value="">Selecione</option>
                                    <?php $query = "SELECT id,nome,cargo,departamento,salario,data_admissao,data_nascimento FROM funcionarios";

                                    $result = mysqli_query($conect, $query);
                                    while ($dados = mysqli_fetch_array($result)) {
                                    ?>
                                        <option value="<?php print $dados['id'] ?>"> <?php print $dados['nome'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Cliente Comprador</label>
                                <select class="form-select" name="cliente" id="cliente">
                                    <option value="">Selecione</option>
                                    <?php $query = "SELECT clientes.id,clientes.nome,cpf,telefone,email, cidades.cidade, cidades.estado FROM clientes 
                        INNER JOIN enderecos ON clientes.id_enderecos = enderecos.id
                        INNER JOIN cidades ON enderecos.id_cidades = cidades.id";

                                    $result = mysqli_query($conect, $query);

                                    while ($dados = mysqli_fetch_array($result)) {
                                    ?>
                                        <option value="<?php print $dados['id'] ?>"><?php print $dados['nome'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </form>


                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    Produtos em Estoque
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table table-bordered table-hover" id="dados">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Marca</th>
                                    <th scope="col">Pre&ccedil;o de Venda</th>
                                    <th scope="col">Código de Barras</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col">Adicionar ao Carrinho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($dados = mysqli_fetch_array($sql_query_produtos)) {
                                ?>
                                    <tr>
                                        <th scope="row"><?php print $dados['id']; ?></th>
                                        <td><?php print $dados['nome']; ?></td>
                                        <td><?php print $dados['marca']; ?></td>
                                        <td><?php print $dados['preco_venda']; ?></td>
                                        <td><?php print $dados['cod_barras']; ?></td>
                                        <td>
                                            <input type="number" class="form-control input_quant" value="0" min="0" max="<?php print floor(doubleval($dados['estoque'])) ?>" name="quantidade_produto-<?php print $dados['id'] ?>" id="quantidade_produto-<?php print $dados['id'] ?>">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm btn-circle" style="color: #fff" title="Adicionar ao Carrinho" onclick="adicionarCarrinho(<?php print $dados['id']; ?>)"><i class="fa fa-shopping-basket"></i></button>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="card" id="card_produtos_venda">
                <div class="card-header">
                    Produtos da Compra
                </div>
                <div class="card-body">
                    <div class="row">
                        <table class="table table table-bordered table-hover" id="venda">
                            <thead>
                                <tr>
                                    <th scope="col">Id do Produto</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Marca</th>
                                    <th scope="col">Pre&ccedil;o de Venda</th>
                                    <th scope="col">Código de Barras</th>
                                    <th scope="col">Quantidade</th>
                                    <th scope="col">Remover do Carrinho</th>
                                </tr>
                            </thead>
                            <tbody id="vendas_body">
                                <?php
                                $query = "SELECT produtos.*, venda_produto.quantidade FROM venda_produto INNER JOIN produtos ON venda_produto.id_produtos = produtos.id WHERE id_vendas = $id_venda";
                                $result = mysqli_query($conect, $query);
                                while ($dados = mysqli_fetch_array($result)) { ?>
                                    <tr>
                                        <th scope="row"><?php print $dados['id'] ?></th>
                                        <td><?php print $dados['nome'] ?></td>
                                        <td><?php print $dados['marca'] ?></td>
                                        <td>R$ <?php print $dados['preco_venda'] ?></td>
                                        <td><?php print $dados['cod_barras'] ?></td>
                                        <td><?php print number_format($dados['quantidade']) ?></td>
                                        <td><button type="button" class="btn btn-danger btn-sm btn-circle" style="color: #fff" title="Remover do Carrinho" onclick="removerCarrinho(<?php print $dados['id']; ?>)"><i class="fa fa-shopping-basket"></i></button></td>
                                    </tr>
                                <?php } ?>


                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    $('#venda').DataTable({
                        "language": {
                            "sProcessing": "Processando...",
                            "sLengthMenu": "Mostrar _MENU_ registros",
                            "sZeroRecords": "Não foram encontrados resultados",
                            "sEmptyTable": "Sem dados disponíveis nesta tabela",
                            "sInfo": "Mostrando registros de _START_ a _END_ em um total de _TOTAL_ registros",
                            "sInfoEmpty": "Mostrando registros de 0 a 0 de um total de 0 registros",
                            "sInfoFiltered": "(filtrado de um total de _MAX_ registros)",
                            "sInfoPostFix": "",
                            "sSearch": "Buscar:",
                            "sUrl": "",
                            "sInfoThousands": ",",
                            "sLoadingRecords": "Carregando...",
                            "oPaginate": {
                                "sFirst": "Primeiro",
                                "sLast": "Último",
                                "sNext": "Seguinte",
                                "sPrevious": "Anterior"
                            },
                            "oAria": {
                                "sSortAscending": ": Ordenar de forma crescente",
                                "sSortDescending": ": Ordenar de forma decrescente"
                            }
                        }
                    });
                </script>

            </div>
        </div>


    </div>
</main>



<script type="text/javascript" language ="Javascript">
    $numero = 0;
    adicionarCarrinho = (id) => {
        $.ajax({
            method: "POST",
            url: 'adiciona_produto_venda.php',
            data: {
                id: id,
                valor: "adicionar",
                cont: $numero,
                id_venda: <?php print $id_venda ?>,
                quantidade: $("#quantidade_produto-" + id).val()
            }
        }).done(function(response) {
            $('#card_produtos_venda').remove();
            if ($numero == 0) {
                $('.listagem-produtos').html($('.listagem-produtos').html() + response)
            } else {

                $('.listagem-produtos').html($('.listagem-produtos').html() + response)
            }
            $numero++;
        })

    }

    removerCarrinho = (id) => {
        Swal.fire({
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            showDenyButton: true,
            denyButtonText: 'Quantidade Espec&iacute;fica',    
            confirmButtonText: 'Remover Todos',
            html: 'Deseja remover TODOS os itens deste produto ou remover uma quantidade espec&iacute;fica?'
           
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: 'adiciona_produto_venda.php',
                    data: {
                        id: id,
                        valor: "remover",
                        id_venda: <?php print $id_venda ?>,
                        remove_tudo: true
                    }
                }).done(function(response) {
                    $('#card_produtos_venda').remove();
                    if ($numero == 0) {
                        $('.listagem-produtos').html($('.listagem-produtos').html() + response)
                    } else {

                        $('.listagem-produtos').html($('.listagem-produtos').html() + response)
                    }

                })
            } else if (result.isDenied) {
                $.ajax({
                    method: "POST",
                    url: 'adiciona_produto_venda.php',
                    data: {
                        id: id,
                        valor: "checar",
                        id_venda: <?php print $id_venda ?>
                    }
                }).done(function(response) {
                  Swal.fire({
                        icon: 'question',
                        text: 'Remover quantos itens?',
                        input: 'number',
                        inputLabel: 'Quantidade',
                        inputAttributes: {
                            min: 1,
                            max: response
                        },
                        confirmButtonText: "Confirmar",
                        showCancelButton: true,
                        cancelButtonText: "Cancelar"
                    }).then((result2) => {
                        if (result2.isConfirmed) {
                            $.ajax({
                                method: "POST",
                                url: 'adiciona_produto_venda.php',
                                data: {
                                    id: id,
                                    valor: "remover",
                                    id_venda: <?php print $id_venda ?>,
                                    quantidade: $('#swal2-input').val()
                                }
                            }).done(function(response) {
                                $('#card_produtos_venda').remove();
                                if ($numero == 0) {
                                    $('.listagem-produtos').html($('.listagem-produtos').html() + response)
                                } else {
                                    $('.listagem-produtos').html($('.listagem-produtos').html() + response)
                                }

                            })
                        }
                    })
                })

            }
        })


    }

    /*  $(":input").bind('keyup mouseup', function() {
        if ($('#quantidade_produto-*').val() != '') {

            $(document).ready(function() {
                let contador = 0;
                let itemA;
                $('.input_quant').each(function() {
                    if ($(this).val() != '' && $(this).val() != 0) {
                        let item = document.createElement('tr');


                        itemA = document.createElement('td');
                        itemA.innerHTML = $(this).val();

                        item.appendChild(itemA);


                        document.getElementById('vendas_body').appendChild(item);
                        contador++;


                    }
                });
            });

        }
    }); */


    //event key enter = 13
    // id da busca = dados_filter
    /*


    */
</script>