<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Tasty Search</title>

        <!-- Styles -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.default.min.css" />
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" />
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome-animation/0.0.10/font-awesome-animation.min.css" />
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/css/tether-theme-basic.min.css" />
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/microplugin/0.0.3/microplugin.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sifter/0.5.2/sifter.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/selectize.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" ></script>
        {{--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/4.0.2/bootstrap-material-design.iife.js" ></script>--}}
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/4.0.2/bootstrap-material-design.css" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/0.3.0/js/material.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/0.3.0/js/ripples.min.js"></script>
        <style>
        </style>
        <style>
            .affix {
                position: fixed;
                top: 20px;
                right: 21%;
                max-height: 100vh;
                overflow: auto;
            }
            .nav .active a {
                padding-left: 18px;
                font-weight: 700;
                color: #563d7c;
                background-color: transparent;
                border-left: 2px solid #563d7c;
            }
            .nav a {
                padding-left: 20px;
                color: #767676;
            }
            body{
                padding-top: 50px;
                width: 60%;
                margin-left: 20%;
            }
            h1, h2,h3, h4 {
                font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;
                font-weight: 300;
            }
            h5 {
                margin-top: 5px;
                margin-bottom: 5px;
            }

            #result{
                height: 0px;
                overflow: auto;
            }

            pre {
                max-height: 200px;
                overflow: auto;
                background: rgba(221, 221, 221, 0.41);
                border-radius: 0.25rem;
                padding: 1.25rem 1rem;
                white-space: pre-wrap;
                box-shadow: inset 0px 0px 12px 2px rgba(217,217,217,0.91);
                line-height: 1.2;
            }

            .jumbotron {
                background: rgba(221, 221, 221, 0.41);
                width:100%;
                height: 216px;
            }
            .d-in-b {
                display: inline-block;
            }

            .jumbotron h1 {
                margin-top: -45px;
            }
            .pull-right {
                float: right;
            }

            .pre-container{
                padding: 0 1.25rem;
            }

            #result-panels-container{
                margin-top: 30px;
            }

            .list-group-item :first-child {
                margin-right: 0.5rem;
            }

            .list-group-item {
                padding: 5px;
            }
            .label, .label.label-default {
                background-color: #9e9e9e;
            }

            .label {
                border-radius: 1px;
                padding: .3em .6em;
            }
            .label-default {
                background-color: #777;
            }
            .label {
                display: inline;
                padding: .2em .6em .3em;
                font-size: 75%;
                font-weight: 700;
                line-height: 1;
                color: #fff;
                text-align: center;
                white-space: nowrap;
                vertical-align: baseline;
                border-radius: .25em;
            }

            .keyword-div {
                word-break: break-all;
                margin-bottom: 15px;
            }

        </style>
    </head>
    <body onscroll="scrolled()" >
        <div class="container" data-spy="scroll">
            <div class="bs-component">
                <div class="flip-container" id="f-container">
                    <div class="front">
                        <div class="jumbotron">
                            <h1>Tasty Search</h1>
                            <p>Simple project on searching docs based on keywords given.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9">
                    <input type="text" id="test" />
                </div>
                <div class="col-md-1 bs-component">
                    <input type="number" max="20" min="1" id="k" class="form-control" placeholder="k"/>
                </div>
                <div class="col-md-2">
                    <button type="button"
                            id="search-btn" class="btn btn-raised btn-default" onclick="callToGetDocs()"
                            style="width: 100%;"
                    >
                        <i class="fa fa-search" aria-hidden="true"></i> Search
                    </button>
                </div>
                <div id="result" class="col-md-12">
                    <div class="bs-component">
                        <div id="result-panels-container" >
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>
    <script>
        $.material.init();
        var sel = $('#test').selectize({
            delimiter: ',',
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input
                }
            },
            placeholder: "Enter keywords without space in      "
        });

        function callToGetDocs() {
            if (!$('#k').val()) $('#k').val(10);
            if (sel.val() == '') {
                $('.selectize-input').addClass('animated flash');
                setTimeout(function() {
                    $('.selectize-input').removeClass('animated flash');
                }, 1000);
                return;
            }
            var data = {
                query: sel.val().split(','),
                k: $('#k').val()
            };
            var time = Date.now();
            $('#search-btn').html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Search');
            $.ajax({
               method: 'POST',
               data: data,
               url: '/query',
               'Content-Type': 'application/json',
               success: function(d) {
                   var resInms = Date.now() - time;
                   var docs = d.result;
                   var reg = new RegExp(d.keywords.join('|'), 'gi');
                   var str = `<h4 style="margin-bottom:20px;" class="d-in-b">Results
                        <sub style="margin-left: 3px;">(in ${resInms}ms)</sub></h4>
                        <button class="btn btn-raised btn-warning d-in-b pull-right" onclick="clearResult()">
                        <i class="fa fa-times" aria-hidden="true"></i> Clear Results</button>
                            ${getSearchedKeywords(d.keywords)}
                        <div>
                            <div>
                              <div class="row"> <div class="col-md-9" id="result-div">
                    `;
                   Object.keys(docs).forEach(function(i) {
                       str += getResultPane(docs[i], reg, i);
                   });
                   str+= `</div>
                        <div class="col-md-3">
                            <nav id="myScrollspy" class="pull-right">
                              <ul class="nav nav-pills nav-stacked">`;
                   Object.keys(docs).forEach(function(i) {
                       str += getLis(i);
                       });
                   str += `
                              </ul>
                             </nav>
                          </div>
                        </div>
                    `;
                   $('#result-panels-container').html(str);

               },
               error: function () {
                   str = `
                <div style="text-align: center;">
                <i class="fa fa-warning fa-2x faa-flash animated"></i>
                <br/>
                <h3 style="text-align=center;"> Oops, It's embarrassing 😧<br/>
                <br/>Server responded with an error<br/><br/> try again</h3></div>`;
                   $('#result-panels-container').html(str);
               },
               complete: function (e) {

                   $('#result').css('height', 'auto');
                   $('#search-btn').html('<i class="fa fa-search" aria-hidden="true"></i> Search');
               }
            });
        }

        function getSearchedKeywords(q) {
            return `
                  <h5>Searched Keywords:</h5>
                  <div class="keyword-div">
                    ${q.map(function(word) {
                        return '<div style="display: inline-block;" class="label label-default">' +word + '</div>';
                    })}
                </div>
            `;
        }
        function getLis(i) {
            i++;
            return `<li id="li-doc${i}" onclick="changeActive(event)" class="${i == 1 ? 'active' : ''}"><a href="#doc${i}">Rank ${i}</a></li>`
        }

        function getResultPane(doc, reg, i) {
            return `
                <div class="card card-outline-primary text-center animated fadeIn" id="doc${(parseInt(i)+1)}">
                  <div class="card-block">
                    <h3 class="card-title">Rank: ${doc.rank} <span class="pull-right">Score: ${doc.matchingScore.toFixed(2)}</span></h3>
                      <div class="card-body pre-container">
                        ${getDocHtml(doc, reg)}
                      </div>
                  </div>
                </div>
            `;
        }

        function getDocHtml(doc, reg) {
            return `
                <div class="list-group">
                    <li class="list-group-item"><b>Product Id :</b>${doc.productId}</li>
                    <li class="list-group-item"><b>User Id :</b>${doc.userId}</li>
                    <li class="list-group-item"><b>Profile Name :</b>${doc.profileName}</li>
                    <li class="list-group-item"><b>Helpfulness :</b>${doc.helpfulness}</li>
                    <li class="list-group-item"><b>Score :</b>${doc.score}</li>
                    <li class="list-group-item"><b>Time :</b>${doc.time}</li>
                    <li class="list-group-item"><b>Review Summary :</b>${doc.summary.replace(reg, function(x) { return '<mark>'+x+'</mark>'})}</li>
                    <li class="list-group-item"><b>Review Text :</b><pre>${doc.text.replace(reg, function(x) { return '<mark>'+x+'</mark>'})}</pre></li>
                </div>
                    `;
        }

        function clearResult() {
            $('#result-panels-container').fadeOut();

            setTimeout(function() {
                $('#result-panels-container').html('');
                $('#result-panels-container').fadeIn();
            });
        }
        function changeActive(e) {
            $('.nav li').removeClass('active');
            $(e.currentTarget).addClass('active');
        }

        var lastId;
        function scrolled(e) {
            if (!$('#myScrollspy').hasClass('affix') && document.body.scrollTop > 450 ) {
                $('#myScrollspy').addClass('affix');
            } else if ($('#myScrollspy').hasClass('affix') && document.body.scrollTop < 450) {
                $('#myScrollspy').removeClass('affix');
            }
            // Get container scroll position
            var fromTop = $(this).scrollTop() + 20;

            // Get id of current scroll item
            var cur = $('.card').filter(function(){
                if ($(this).offset().top < fromTop)
                    return true;
                return false;
            });
            // Get the id of the current element
            cur = cur[cur.length-1];

            var id = cur ? cur.id : "";
            if (id && lastId !== id) {
                lastId = id;
                $('.nav li').removeClass('active animated jello');
                $('#li-'+id).addClass('active animated jello');
            }
        }

    </script>
</html>
