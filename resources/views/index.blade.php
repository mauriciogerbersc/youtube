<!DOCTYPE html>

<head>
    <title>YouTube Finder</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>

    <!-- Modal -->
    <div class="modal fade" id="modalTempoSemanal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <form>
                        @csrf
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">Limite Semanal (em minutos):</label>
                            <select class="form-control" id="tempoSemanal">
                                <option value="15">15 m</option>
                                <option value="20">20 m</option>
                                <option value="30">30 m</option>
                                <option value="40">40 m</option>
                                <option value="90">90 m</option>
                                <option value="120">120 m</option>
                                <option value="150">150 m</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="salvarLimite">Definir limite</button>
                </div>
            </div>
        </div>
    </div>

    <main role="main">

        <section class="jumbotron">
            <div class="row justify-content-center">

                <div class="col-12 col-md-10 col-lg-8">
                    <form class="card card-sm" name="search">
                        @csrf
                        <div class="card-body row no-gutters align-items-center">
                            <div class="col">
                                <input class="form-control form-control-lg form-control-borderless" type="search" name="q" placeholder="Busca">
                            </div>
                            <!--end of col-->
                            <div class="col-auto">
                                <button class="btn btn-lg btn-success" type="submit">Buscar</button>
                            </div>
                            <!--end of col-->
                        </div>
                    </form>

                    <div class="mt-4" id="validationSignup"></div>

                </div>
                <!--end of col-->
            </div>

        </section>

        <div class="album py-5 bg-light">

            <div id='loader' style='display: none;'>
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Carregando...</span>
                    </div>
                </div>
            </div>
            <div class="container resultadoPesquisa">
                <div class="row hotSearch mb-3"></div>
                <div class="row result">

                </div>
            </div>
        </div>

    </main>

</body>

<!-- Latest compiled JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
<script src="{{ asset('js/scripts.js')  }}"></script>

<script type="text/javascript" charset="utf-8">
    checarTempo();
    
    function checarTempo() {
        $.ajax({
            url: "{{ route('tempo_utilizado') }}",
            type: "GET",
            dataType: 'json',
            success: function(response) {
                if (response.success === false) {
                    $('#modalTempoSemanal').modal('show')
                }
            }
        });
    }

    $("#salvarLimite").click(function(e) {
        e.preventDefault();

        var formData = {
            tempoSemanal: $("#tempoSemanal option:selected").val(),
        }

        $.ajax({
            url: "{{ route('definir_sessao') }}",
            type: "GET",
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success !== false) {
                    alert(response.retorno);
                    $('#modalTempoSemanal').modal('hide');
                }
            }
        });

    });


    $('form[name=search]').submit(function(event) {
        event.preventDefault();

        $.ajax({
            url: "{{ route('buscar_video') }}",
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                $(".hotSearch").html('');
                $(".result").html('');
                $("#loader").show();
            },
            error: function(data) {
                errorsHtml = '<div class="alert alert-danger">Erro na Pesquisa. Refaça os termos</div>';
                $('#validationSignup').html(errorsHtml);
                $("#validationSignup").removeClass('d-none');
            },
            success: function(response) {
                if (response.success === true) {

                    let arr = $.map(response['retorno'], function(el) {
                        return el;
                    });

                    OnSuccessFillDiv(arr);
                } else {
                    errorsHtml = '<div class="alert alert-danger">' + response.mensagem + '</div>';
                    $('#validationSignup').html(errorsHtml);
                    $("#validationSignup").removeClass('d-none');
                }
            },
            complete: function(data) {
                $("#loader").hide();
            }
        });
    });



    function OnSuccessFillDiv(result) {
        console.log(result)
        var htmlContent = "";
        var title = [];
        $.each(result, function(key, value) {
            htmlContent += '<div class="col-md-4">';
            htmlContent += '<div class="card mb-4 shadow-sm">';
            htmlContent += '<img src="' + value['thumb'] + '" />';
            htmlContent += '<div class="card-body">';
            htmlContent += '<p class="card-text">' + value['title'] + '</p>';
            htmlContent += '<div class="d-flex justify-content-between align-items-center">';
            htmlContent += '<div class="btn-group">';
            htmlContent += value['button'];
            htmlContent += '</div>';
            htmlContent += '<small class="text-muted"> ' + value['duration'] + ' </small>';
            htmlContent += '</div>';
            htmlContent += '</div>';
            htmlContent += '</div>';
            htmlContent += '</div>';

            title.push(value['title']);
        });

        var htmlHotTitle = "<div class='d-flex justify-content-between'>";
        $.each(stringCount(title, " "), function(k, v) {
            htmlHotTitle += '<div class="mr-3"><span class="badge badge-primary">' + k + ' (' + v + ')</span></div>';
        })
        htmlHotTitle += "</div>";

        $(".hotSearch").html(htmlHotTitle);
        $(".result").html(htmlContent);

    }
</script>

</html>