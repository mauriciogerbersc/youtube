<!DOCTYPE html>

<head>
    <title>YouTube Finder</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>
    <main role="main">
        <section class="jumbotron text-center">

            @if(isset($id))
            <div class="container">
                <div class="d-flex justify-content-center">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/{{$id}}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
                    </iframe>
                </div>
                <div class="container">
              
                <p class="lead text-muted">Seu saldo atual é: <strong>{{$saldoAtual}}</strong> e o vídeo possuí <strong>{{$tamanhOVideo}}</strong></p>
                <p><a href="{{ url()->previous() }}" class="btn btn-primary my-2"> << Voltar</a></p>
             </div>
            </div>
            @else
            <div class="container">
                <h1>{{$mensagem}}</h1>
                <p class="lead text-muted">Seu saldo atual é: <strong>{{$saldoAtual}}</strong> e o vídeo possuí <strong>{{$tamanhOVideo}}</strong></p>
                <p><a href="{{ url()->previous() }}" class="btn btn-primary my-2"> << Voltar</a></p>
             </div>
         @endif 
        </section> 
    </main> 
</body>
</html>