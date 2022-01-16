

<style type="text/css">
    #container7, #container8 {
      height: 600px;
    }
    #container6, #container9 {
      height: 500px;
    }
    #container2 {
      height: 320px;
    }
    #container1 {
      height: 300px;
    }
    caption.highcharts-table-caption {
      display: none;
    }

    .highcharts-figure .highcharts-root {
      font-family: inherit !important;
    }
    .highcharts-data-table table {
    	border: 1px solid #EBEBEB;
    	margin: 10px auto;
    	text-align: center;
    	width: 100%;
    }
    .highcharts-data-table caption {
        padding: 1em 0;
        color: #555;
    }
    .highcharts-data-table th {
      padding: 0.5em;
    	background-color: initial;
    	color: initial;
    	border-color: initial;
    	position: initial;
    	border-left: initial;
    }
    .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
      padding: 0.5em;
    	border-left: initial;
    }
    .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }
    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }
    .icon {
      min-width: 1rem;
    }

</style>
<div class="container-lg">
  @include('inc.toast')

<h3 class="m-3 text-center">Статистика по проекту {{ $info['project_name'] }}</h3>

@include('inc.breadcrumbs')

<p class="alert alert-info">Статистика показывается по той выборке, которую вы формируете и видите перед тем, как нажать кнопку. Это может быть проект целиком, отдельное правило, или выборка с применением тех или иных фильтров</p>

  <div class="row">
    <div class="col-md-12 my-5">


      <div class="form-group d-flex flex-wrap justify-content-around align-items-center">
          Общее количество авторов за последние:
          <button type="button" query_string="{{ serialize($request) }}" class="my-2 btn btn-sm btn-primary vk-top-bg text-white period no-outline" name="period" mode = "day"><i class="icon fas fa-calendar-day"></i><span class="spinner-border spinner-border-sm d-none"></span> 12 дней</button>
          <button type="button" query_string="{{ serialize($request) }}" class="my-2 btn btn-sm btn-primary vk-top-bg text-white period no-outline"  name="period" mode = "week"><i class="icon fas fa-calendar-week"></i><span class="spinner-border spinner-border-sm d-none"></span> 12 недель</button>
          <button type="button" query_string="{{ serialize($request) }}" class="my-2 btn btn-sm btn-primary vk-top-bg text-white period no-outline"  name="period" mode = "month"><i class="icon fas fa-calendar-alt"></i><span class="spinner-border spinner-border-sm d-none"></span> 12 месяцев</button>
      </div>
      <script type="text/javascript">
          $(document).ready( function () {
            $(document).on('click', '.period', function (e) {
              e.preventDefault();
              _this = $(this);
              var mode = $(this).attr("mode");
              var query_string = $(this).attr("query_string");
              $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '{{ route('period-stat', $info['project_name']) }}',
                data: {'mode' : mode, 'query_string' : query_string},
                beforeSend: function () {
                          _this
                          .prop('disabled', true)
                          .find('.icon').addClass('d-none');
                          _this.find('.spinner-border-sm').removeClass('d-none');
                },
                success: function(data){
                  if (data.success) {
                      _this
                      .prop('disabled', false)
                      .find('.icon').removeClass('d-none');
                      _this.find('.spinner-border-sm').addClass('d-none');
                    $("#content-graph").html(data.html);
                  } else {
                    $('.toast-header').addClass('bg-danger');
                    $('.toast-header').removeClass('bg-success');
                    $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                    $('.toast').toast('show');
                  }}
              });
            });
          } );
      </script>
      <div id="content-graph">@include('streaming.period-stat')</div>

    </div>
  </div>

  <div class="row">
    <div class="col-md-6 my-5">
        <figure class="highcharts-figure">
            <div id="container1"></div>
            <p class="highcharts-description">
            </p>
        </figure>
        <script type="text/javascript">
          Highcharts.chart('container1', {
              chart: {
                  plotBackgroundColor: null,
                  plotBorderWidth: null,
                  plotShadow: false,
                  type: 'pie'
              },
              title: {
                  text: 'Распределение авторов по полу'
              },
              tooltip: {
                  pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              accessibility: {
                  point: {
                      valueSuffix: '%'
                  }
              },
              plotOptions: {
                  pie: {
                      allowPointSelect: true,
                      cursor: 'pointer',
                      dataLabels: {
                          enabled: true,
                          format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                      }
                  }
              },
              series: [{
                  name: 'Записи',
                  colorByPoint: true,
                  data: [
                    @if ($stat['male'] > 0)
                    		    {
                                name: 'Муж',
                                y: {{ $stat['male'] }}
                            },
                    @endif
                    @if ($stat['female'] > 0)
                    		    {
                                name: 'Жен',
                                y: {{ $stat['female'] }},
                                sliced: true,
                                selected: true
                            },
                    @endif
            	    ]
              }]
          });
        </script>
    </div>

    <div class="col-md-6 my-5">
        <figure class="highcharts-figure">
            <div id="container2"></div>
            <p class="highcharts-description">
            </p>
        </figure>
        <script type="text/javascript">
          Highcharts.chart('container2', {
              chart: {
                  plotBackgroundColor: null,
                  plotBorderWidth: null,
                  plotShadow: false,
                  type: 'pie'
              },
              title: {
                  text: 'Распределение авторов'
              },
              tooltip: {
                  pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              accessibility: {
                  point: {
                      valueSuffix: '%'
                  }
              },
              plotOptions: {
                  pie: {
                      allowPointSelect: true,
                      cursor: 'pointer',
                      dataLabels: {
                          enabled: true,
                          format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                      }
                  }
              },
              series: [{
                  name: 'Пользователи',
                  colorByPoint: true,
                  data: [
                      @if ($stat['male'] > 0)
                  		    {
                              name: 'Муж',
                              y: {{ $stat['male'] }}
                          },
                  	  @endif
                      @if ($stat['female'] > 0)
                  		    {
                              name: 'Жен',
                              y: {{ $stat['female'] }}
                          },
                      @endif
                      @if ($stat['groups'] > 0)
                      		{
                      			name: 'Группы',
                      			y: {{ $stat['groups'] }},
                                  sliced: true,
                                  selected: true
                      		},
                      @endif
          	       ]
              }]
          });
        </script>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 mx-auto my-5">
      <figure class="highcharts-figure">
          <div id="container3"></div>
          <p class="highcharts-description">
          </p>
      </figure>
      <script type="text/javascript">
        Highcharts.chart('container3', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Распределение авторов по возрасту'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Пользователи',
                colorByPoint: true,
                data: [
                  @if ($ages['_0'] > 0)
                  		   // {
                         //      name: 'неопределено',
                         //      y: {{ $ages['_0'] }},
                         //      sliced: true,
                         //      selected: true
                         //  },
                  @endif
                  @if ($ages['_18'] > 0)
                  		  {
                              name: '13 - 17',
                              y: {{ $ages['_18'] }}
                          },
                  @endif
                  @if  ($ages['18_24'] > 0)
                  		  {
                              name: '18 - 24',
                              y: {{ $ages['18_24'] }}
                          },
                  @endif
                  @if  ($ages['25_34'] > 0)
                  		  {
                              name: '25 - 34',
                              y: {{ $ages['25_34'] }}
                          },
                  @endif
                  @if  ($ages['35_44'] > 0)
                  		  {
                              name: '35 - 44',
                              y: {{ $ages['35_44'] }},
                              sliced: true,
                              selected: true
                          },
                  @endif
                  @if  ($ages['45_54'] > 0)
                  		  {
                              name: '45 - 54',
                              y: {{ $ages['45_54'] }}
                          },
                  @endif
                  @if  ($ages['55_'] > 0)
                  		{
                  			name: '55+',
                  			y: {{ $ages['55_'] }}
                  		},
                  @endif
        	      ]
            }]
        });
      </script>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 my-5">
      <figure class="highcharts-figure">
          <div id="container4"></div>
          <p class="highcharts-description">
          </p>
      </figure>
      <script type="text/javascript">
        Highcharts.chart('container4', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Распределение авторов <br />(<b>пользователей</b>) по количеству подписчиков'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                    }
                }
            },
            series: [{
                name: 'Авторы',
                colorByPoint: true,
                data: [
                  @if ($stat['follow_0'] > 0)
                  		{
                              name: '0',
                              y: {{ $stat['follow_0'] }},
                              sliced: true,
                              selected: true
                          },
                  @endif
                  @if ($stat['follow_500'] > 0)
                  		{
                              name: 'до 50',
                              y: {{ $stat['follow_500'] }},
                              sliced: true,
                              selected: true
                          },
                  @endif
                  @if ($stat['follow_501_1000'] > 0)
                  		{
                              name: '51-100',
                              y: {{ $stat['follow_501_1000'] }}
                          },
                  @endif
                  @if ($stat['follow_1001_5000'] > 0)
                  		{
                              name: '101-500',
                              y: {{ $stat['follow_1001_5000'] }}
                          },
                  @endif
                  @if ($stat['follow_5001_10000'] > 0)
                  		{
                              name: '501-1000',
                              y: {{ $stat['follow_5001_10000'] }}
                          },
                  @endif
                  @if ($stat['follow_10001_30000'] > 0)
                  		{
                              name: '1001-3000',
                              y: {{ $stat['follow_10001_30000'] }}
                          },
                  @endif
                  @if ($stat['follow_30001_'] > 0)
                  		{
                  			name: '3000+',
                  			y: {{ $stat['follow_30001_'] }}
                  		},
                  @endif
        	      ]
            }]
        });
      </script>
    </div>

    <div class="col-md-6 my-5">
      <figure class="highcharts-figure">
          <div id="container5"></div>
          <p class="highcharts-description">
          </p>
      </figure>
      <script type="text/javascript">
        Highcharts.chart('container5', {
          chart: {
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              type: 'pie'
          },
          title: {
              text: 'Распределение авторов <br />(<b>групп</b>) по количеству подписчиков'
          },
          tooltip: {
              pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
          },
          accessibility: {
              point: {
                  valueSuffix: '%'
              }
          },
          plotOptions: {
              pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  dataLabels: {
                      enabled: true,
                      format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                  }
              }
          },
          series: [{
              name: 'Авторы',
              colorByPoint: true,
              data: [
                @if ($stat['group_follow_500'] > 0)
                    {
                            name: 'до 500',
                            y: {{ $stat['group_follow_500'] }},
                            sliced: true,
                            selected: true
                        },
                @endif
                @if ($stat['group_follow_501_1000'] > 0)
                    {
                            name: '501-1000',
                            y: {{ $stat['group_follow_501_1000'] }}
                        },
                @endif
                @if ($stat['group_follow_1001_5000'] > 0)
                    {
                            name: '1001-5000',
                            y: {{ $stat['group_follow_1001_5000'] }}
                        },
                @endif
                @if ($stat['group_follow_5001_10000'] > 0)
                    {
                            name: '5001-10000',
                            y: {{ $stat['group_follow_5001_10000'] }}
                        },
                @endif
                @if ($stat['group_follow_10001_30000'] > 0)
                    {
                            name: '10001-30000',
                            y: {{ $stat['group_follow_10001_30000'] }}
                        },
                @endif
                @if ($stat['group_follow_30001_'] > 0)
                    {
                      name: '30000+',
                      y: {{ $stat['group_follow_30001_'] }}
                    },
                @endif
              ]
          }]
        });
      </script>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 my-5">
      <div id="container6">
      </div>
      <script type="text/javascript">
          var elevationData = [
            @php $j = 1; @endphp
          @for ($i = 13; $i <= 118; $i++)
              @if (!empty($full_age[$j]['age']) && $full_age[$j]['age'] == $i)
                [{{ $full_age[$j]['age'] }}, {{ $full_age[$j]['cnt'] }}],
                @php $j++; @endphp
              @else [{{ $i }}, 0],
              @endif
          @endfor
          ];

          Highcharts.chart('container6', {
              chart: {
                  type: 'area',
                  zoomType: 'x',
                  panning: true,
                  panKey: 'shift',
                  scrollablePlotArea: {
                      minWidth: 600
                  }
              },

              caption: {
                  text: 'Некоторые пользователи ВК устанавливают себе возраст, достойный кавказских аксакалов. Всплески после отметки показывают наиболее популярные цифры возрастов ветеранов Куликовской битвы.'
              },

              title: {
                  text: 'Подробное распределение пользователей по возрастам'
              },

              accessibility: {
                  description: 'Описание'
              },

              credits: {
                  enabled: false
              },

          annotations: [{
                  labels: [{
                      point: {
                          xAxis: 0,
                          yAxis: 0,
                          x: 76,
                          y: 20
                      },
                      x: -10,
                      text: 'Отметка'
                  },]
              }],
              xAxis: {
                  labels: {
                      format: '{value}'
                  },
                  minRange: 13,
                  title: {
                      text: 'Возраст'
                  },
                  accessibility: {
                      rangeDescription: 'Диапазон'
                  }
              },
              yAxis: {
                  startOnTick: false,
                  endOnTick: true,
                  maxPadding: 0.15,
                  title: {
                      text: 'Количество авторов'
                  },
                  labels: {
                      format: '{value}'
                  }
              },

              tooltip: {
                  headerFormat: 'Возраст: {point.x:.0f}<br>',
                  pointFormat: '{point.y} чел.',
                  shared: true
              },

              legend: {
                  enabled: false
              },

              series: [{
                  accessibility: {
                      keyboardNavigation: {
                          enabled: false
                      }
                  },
                  data: elevationData,
                  lineColor: Highcharts.getOptions().colors[1],
                  color: Highcharts.getOptions().colors[2],
                  fillOpacity: 0.5,
                  name: 'Elevation',
                  marker: {
                      enabled: false
                  },
                  threshold: 0
              }]
          });
      </script>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 my-5">
      <div id="container9">
      </div>
      <script type="text/javascript">

          var data = [
            @foreach ($country_score1 as $code => $value)
              ['{{ mb_strtolower($code) }}', {{ $value }}],
            @endforeach
          ];

          Highcharts.mapChart('container9', {
              chart: {
                  map: 'custom/world-robinson-highres'
              },

              title: {
                  text: 'Распределение авторов по странам мира'
              },

              subtitle: {
                  text: ''
              },

              mapNavigation: {
                  enabled: false,
                  buttonOptions: {
                      verticalAlign: 'bottom'
                  }
              },

              colorAxis: {
                  min: 0
              },

              series: [{
                  data: data,
                  name: 'Авторы',
                  states: {
                      hover: {
                          color: '#BADA55'
                      }
                  },
                  dataLabels: {
                      enabled: false,
                      format: '{point.name}'
                  }
              }]
          });
      </script>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 my-5">
      <figure class="highcharts-figure">
          <div id="container7">
          </div>
          <p class="highcharts-description">
          </p>
      </figure>
      <script type="text/javascript">
          Highcharts.chart('container7', {
              chart: {
                  type: 'column'
              },
              title: {
                  text: 'Распределение авторов по городам'
              },
              subtitle: {
                  text: 'Кликните на столбик, чтобы увидеть подробности'
              },
              accessibility: {
                  announceNewData: {
                      enabled: true
                  }
              },
              xAxis: {
                  type: 'category'
              },
              yAxis: {
                  title: {
                      text: 'Доля авторов'
                  }

              },
              legend: {
                  enabled: false
              },
              plotOptions: {
                  series: {
                      borderWidth: 0,
                      dataLabels: {
                          enabled: true,
                          format: '{point.y:.0f}'
                      }
                  }
              },

              tooltip: {
                  headerFormat: '<span>{series.name}</span><br>',
                  pointFormat: '<span>{point.name}</span>: <b>{point.y:.0f}</b><br/>'
              },

              series: [
                  {
                      name: "Авторы",
                      colorByPoint: true,
                      data: [
          		         @for ($i=0; $i<min($region_count, $region_quantity); $i++)
                          {
                              name: "{{ $region_score[$i]["region"] }}",
                              y: {{ $region_score[$i]["region_score"] }},
                              drilldown:
                              @if ($city_score[$i]['city_count'] == 1 && ($region_score[$i]['region'] == $mos || $region_score[$i]['region'] == $spb))
                                "NULL"
                              @else "{{ $region_score[$i]['region'] }}"
                              @endif

                          },
                    		@endfor
                    		@if ($region_count > $region_quantity)
                    				{
                    					name: "Другие",
                    					y: {{ $other_regions }},
                    					drilldown: "other_regions"
                    				}
                        @endif
                      ]
                  }
              ],
              drilldown: {
                  series: [
                		@for ($i=0; $i<min($region_count, $region_quantity); $i++)
                            {
                                name: "{{ $region_score[$i]['region'] }}",
                                id: "{{ $region_score[$i]['region'] }}",
                                data: [
                			      @for ($j=0; $j<(min ($city_score[$i]['city_count'], region_quantity)); $j++)
                					          [
                                        "{{ $city_score[$i][$j]['city'] }}",
                                        {{ $city_score[$i][$j]['city_score'] }}
                                    ],
                            @endfor

                                ]
                            },
                		@endfor
                		@if ($region_count > $region_quantity)
                			     {
                                name: "Другие",
                                id: "other_regions",
                                data: [

                          				@for ($i=$region_quantity; $i<min($region_count, 3*$region_quantity); $i++)
                          					    [
                                          "{{ $region_score[$i]['region'] }}",
                                          {{ $region_score[$i]['region_score'] }}
                                        ],
                          				@endfor
                                ]
                            },
                		@endif
                  ]
                }
              })
      </script>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 my-5">
      <figure class="highcharts-figure">
          <div id="container8"></div>
          <p class="highcharts-description">
          </p>
      </figure>
      <script type="text/javascript">
          Highcharts.chart('container8', {
              chart: {
                  type: 'column'
              },
              title: {
                  text: 'Авторы из стран/городов за пределами РФ'
              },
              subtitle: {
                  text: 'Кликните на столбик, чтобы увидеть подробности'
              },
              accessibility: {
                  announceNewData: {
                      enabled: true
                  }
              },
              xAxis: {
                  type: 'category'
              },
              yAxis: {
                  title: {
                      text: 'Доля авторов'
                  }

              },
              legend: {
                  enabled: false
              },
              plotOptions: {
                  series: {
                      borderWidth: 0,
                      dataLabels: {
                          enabled: true,
                          format: '{point.y:.0f}'
                      }
                  }
              },

              tooltip: {
                  headerFormat: '<span>{series.name}</span><br>',
                  pointFormat: '<span>{point.name}</span>: <b>{point.y:.0f}</b><br/>'
              },

              series: [
                  {
                      name: "Авторы",
                      colorByPoint: true,
                      data: [
                          		@for ($i=0; $i<min($country_count, $region_quantity); $i++)
                                          {
                                              name: "{{ $country_score[$i]["country"] }}",
                                              y: {{ $country_score[$i]["country_score"] }},
                                              drilldown: "{{ $country_score[$i]["country"] }}"
                                          },
                          		@endfor
                          			@if ($country_count > $region_quantity)
                          				{
                          					name: "Другие",
                          					y: '.$other_countries.',
                          					drilldown: "other_countries"
                          				}
                                @endif

                            ]
                  }
              ],
              drilldown: {
                  series: [
                		@for ($i=0; $i<min($country_count, $region_quantity); $i++)
                            {
                                name: "{{ $country_score[$i]["country"] }}",
                                id: "{{ $country_score[$i]["country"] }}",
                                data: [

                                			@for ($j=0; $j<(min ($forein_city_score[$i]['forein_city_count'], region_quantity)); $j++)
                                					[
                                						@if (!empty($forein_city_score[$i][$j]['city'])) "{{ $forein_city_score[$i][$j]['city'] }}"
                                            @else 'Не указано'
                                            @endif
                                            , {{ $forein_city_score[$i][$j]['forein_city_score'] }}
                                          ],
                                			@endfor

                                ]
                            },
                		@endfor
                		@if ($region_count > $region_quantity)
                			{
                                name: "Другие",
                                id: "other_countries",
                                data: [

                          				@for ($i=region_quantity; $i<min($country_count, 3*$region_quantity); $i++)
                          					[
                                                  "{{ $country_score[$i]['country'] }}",
                                                  {{ $country_score[$i]['country_score'] }}
                                    ],
                                  @endfor
                                ]
                        },
                		@endif

                ]
              }
          });
      </script>
    </div>
  </div>

@if (!empty($weight_cloud))
  <div class="row">
    <div class="col-md-12 my-5">
      <figure class="highcharts-figure">
          <div id="container10"></div>
          <p class="highcharts-description">
          </p>
      </figure>
      <script type="text/javascript">
          var data = [];
          @for ($i=0; $i<count($weight_cloud); $i++)
            data[{{ $i }}] = {
                name: "{{ $weight_cloud[$i]->name }}",
                weight: "{{ $weight_cloud[$i]->weight }}"
            }
          @endfor

          Highcharts.chart('container10', {
              accessibility: {
                  screenReaderSection: {
                      beforeChartFormat: '<h5>{chartTitle}</h5>' +
                          '<div>{chartSubtitle}</div>' +
                          '<div>{chartLongdesc}</div>' +
                          '<div>{viewTableButton}</div>'
                  }
              },
              series: [{
                  type: 'wordcloud',
                  data: data,
                  name: 'Частота'
              }],
              title: {
                  text: 'Облако слов по проекту {{ $info['project_name'] }}'
              }
          });
      </script>
    </div>
  </div>
@endif

@if (!empty($weight_tag))
  <div class="row">
    <div class="col-md-12 my-5">
      <figure class="highcharts-figure">
          <div id="container11"></div>
          <p class="highcharts-description">
          </p>
      </figure>
      <script type="text/javascript">
          var data = [];
          @for ($i=0; $i<count($weight_tag); $i++)
            data[{{ $i }}] = {
                name: "{{ $weight_tag[$i]->name }}",
                weight: "{{ $weight_tag[$i]->weight }}"
            }
          @endfor

          Highcharts.chart('container11', {
              accessibility: {
                  screenReaderSection: {
                      beforeChartFormat: '<h5>{chartTitle}</h5>' +
                          '<div>{chartSubtitle}</div>' +
                          '<div>{chartLongdesc}</div>' +
                          '<div>{viewTableButton}</div>'
                  }
              },
              series: [{
                  type: 'wordcloud',
                  data: data,
                  name: 'Частота'
              }],
              title: {
                  text: 'Облако тэгов по проекту {{ $info['project_name'] }}'
              }
          });
      </script>
    </div>
  </div>
@endif

  <div class="row">
      <div class="col-md-12 my-5">
        <h4 class="text-center">Список активных авторов</h4>
        <p class="text-center" style="font-size: 13px">Активные авторы — это авторы, которые оставили 3 и более публикации по вашей теме. Всех авторов можно собрать в разделе «Фильтры», нажав на кнопку «Авторы».</p>
        <p class="activnost alert alert-info">Нажмите на цифру в столбике "Активность", чтобы увидеть все активные посты этого автора</p>
        <div class="my-2 p-1 d-flex border rounded justify-content-between align-items-center">
          <div class="form-inline">
            С отмеченными:
            <button type="button" id="del-btn" class="btn btn-sm btn-outline-danger mx-2"><i class="icon far fa-trash-alt"></i><span class="spinner-border spinner-border-sm d-none"></span> Удалить</button>
            <div class="m-0 form-group form-check">
              <input type="checkbox" class="form-check-input" id="ignore_author">
              <label class="form-check-label" for="ignore_author">и больше не собирать</label>
            </div>
          </div>
          <button id="ignored_authors_btn" data-toggle="popover-ignore" class="btn btn-sm btn-outline-info no-outline">Игнор-лист</button>
          <script type="text/javascript">
            $(function () {
                $('[data-toggle="popover-ignore"]').popover({
                container: 'body',
                html: true,
                placement: 'top',
                sanitize: false,
                title: `Список игнорируемых авторов:`,
                content:
                `<div id="ignore-list"></div>`
                })
            });

            $('body').on('click', '#ignored_authors_btn', function() {
                $.ajax({
                  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                  type: 'POST',
                  url: '{{ route('ignore-list', $info['project_name']) }}',

                  success: function(data){
                    if (data.success) {
                      $('#ignore-list').html(data.html);
                      var body = document.getElementsByTagName('body')[0];
                        var event = new CustomEvent("scroll", {
                          detail: {
                            scrollTop: 1
                          }
                        });
                        window.dispatchEvent(event);

                    } else {
                      $('.toast-header').addClass('bg-danger');
                      $('.toast-header').removeClass('bg-success');
                      $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                      $('.toast').toast('show');
                    }
                  }
                });
            });

            $('body').on('click', '.fa-times', function() {
              var clickId = $(this).attr("ignoreid");
              var _this = $(this);
                $.ajax({
                  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                  type: 'POST',
                  url: '{{ route('del-from-ignore', $info['project_name']) }}',
                  data: {'vkid' : {{ session('vkid') }}, 'id' : clickId},
                  success: function(data){
                    if (data.success) {
                      _this.parent().remove();
                      $('.toast-header').addClass('bg-success');
                      $('.toast-header').removeClass('bg-danger');
                      $('.toast-body').html(data.success);
                      $('.toast').toast('show');
                    } else {
                      $('.toast-header').addClass('bg-danger');
                      $('.toast-header').removeClass('bg-success');
                      $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                      $('.toast').toast('show');
                    }
                  }
                });
            });

            $("#del-btn").click(function(){
            	let author_ids = "";
            	let ignore_author = "";
              var _this = $(this);
            	$("input[id*=_del]:checkbox:checked").each(function(){
            		if ($(this).val()=="on") author_ids+=($(this).attr("id").replace("_del", "") + ",");
            	});
            	$("input[id*=ignore_author]:checkbox:checked").each(function(){
            		if ($(this).val()=="on") ignore_author="on";
            	});
            	if (author_ids) {
            		author_array = author_ids.split(",");
            						$.ajax({
                          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                          type: 'POST',
                          url: '{{ route('add-to-ignore', $info['project_name']) }}',
                          data: {'vkid' : {{ session('vkid') }}, 'ignore' : ignore_author, 'author_id' : author_ids},
                          beforeSend: function () {
                                  _this
                                    .prop('disabled', true)
                                    .find('.icon').addClass('d-none');
                                    _this.find('.spinner-border-sm').removeClass('d-none');
                          },
            							success: function(data){
                            if (data.success) {
            									author_array.forEach(function(item, i, arr) {
            										if (item) {
            											item = item.replace("_del", "");
            											var elem = document.getElementById(item);
            											while (elem.firstChild) {
            												elem.removeChild(elem.firstChild);
            											}
            										}
            									});
                              $('.toast-header').addClass('bg-success');
                              $('.toast-header').removeClass('bg-danger');
                              $('.toast-body').html(data.success);
                              $('.toast').toast('show');
            								$("#all_checkbox").prop("checked", false);
                            _this
                              .prop('disabled', false)
                              .find('.icon').removeClass('d-none');
                              _this.find('.spinner-border-sm').addClass('d-none');
            							} else {
                              $('.toast-header').addClass('bg-danger');
                              $('.toast-header').removeClass('bg-success');
                              $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                              $('.toast').toast('show');
                            }}
            						});
            	}
            });
          </script>
        </div>
        <div id="controls-checkbox">
          <div class="table-responsive">
              <table id="table-active" class="sortable table table-striped table-hover table-sm">
                <thead class="thead-dark">
                  <tr>
                    <th>№</th>
                    <th class="p-1 text-center"><input type="checkbox" id="all_checkbox" data-toggle="tooltip" title="Выбрать все"></th>
                    <th>Автор</th>
                    <th>Город</th>
                    <th><i data-toggle="tooltip" title="Пользователь" class='far fa-user'></i><i data-toggle="tooltip" title="Группа" class='fas fa-users'></i></th>
                    <th>Подписч.</th>
                    <th class="text-right">Активн.</th>
    	              <th>Посты</th>
                    <th>Стены<br>постов</th>
                    <th>Обсужд.</th>
                    <th>Стены<br>обсужд.</th>
                    <th>Комм.</th>
                    <th>Стены<br>комм.</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($author_scores as $author_score)
                    <tr id="{{ $author_score['author_id'] }}">
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-center"><input id="{{ $author_score['author_id'] }}_del" type="checkbox"></td>
                        <td class="group-name text-truncate text-nowrap text-left"><a rel="nofollow" target="_blank" href="@if($author_score['author_id'] > 0) https://vk.com/id{{ $author_score['author_id'] }} @else https://vk.com/public{{ -$author_score['author_id'] }} @endif ">{{ $author_score['name'] }}</a></td>
                        <td>{{ $author_score['city'] }}</td>
                        <td>{!! $author_score['author_id'] > 0? "<i class='far fa-user'></i>" : "<i class='fas fa-users'></i>" !!}</td>
                        <td class="text-center">{{ $author_score['members_count'] }}</td>
                        <td class="text-center"><a query_string="{{ serialize($request) }}" mode="all" idauthor="{{ $author_score['author_id'] }}" class="author cursor-pointer"><span class="icon d-inline-block">{{ $author_score['author_score'] }}</span><span class="spinner-border spinner-border-sm d-none"></span></a></td>
                        <td class="text-center">
                          @if ($author_score['author_id_post'] > 0)
                            <a query_string="{{ serialize($request) }}" mode="post" idauthor="{{ $author_score['author_id'] }}" class="author cursor-pointer"><span class="icon d-inline-block">{{ $author_score['author_id_post'] }}</span><span class="spinner-border spinner-border-sm d-none"></span></a>
                          @else
                            {{ $author_score['author_id_post'] }}
                          @endif
                        </td>
                        <td class="text-center">{{ $author_score['author_id_post_unique'] }}</td>
                        <td class="text-center">
                          @if ($author_score['author_id_topic'] > 0)
                            <a query_string="{{ serialize($request) }}" mode="topic_post" idauthor="{{ $author_score['author_id'] }}" class="author cursor-pointer"><span class="icon d-inline-block">{{ $author_score['author_id_topic'] }}</span><span class="spinner-border spinner-border-sm d-none"></span></a>
                          @else
                            {{ $author_score['author_id_topic'] }}
                          @endif
                        </td>
                        <td class="text-center">{{ $author_score['author_id_topic_unique'] }}</td>
                        <td class="text-center">
                          @if ($author_score['author_id_comment'] > 0)
                            <a query_string="{{ serialize($request) }}" mode="comment" idauthor="{{ $author_score['author_id'] }}" class="author cursor-pointer"><span class="icon d-inline-block">{{ $author_score['author_id_comment'] }}</span><span class="spinner-border spinner-border-sm d-none"></span></a>
                          @else
                            {{ $author_score['author_id_comment'] }}
                          @endif
                        </td>
                        <td class="text-center">{{ $author_score['author_id_comment_unique'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          <script type="text/javascript">
              $(document).ready( function () {
                $(".author").click(function() {
                  _this = $(this);
      						var elem = $(this).attr("idauthor");
      						var mode = $(this).attr("mode");
                  var query_string = $(this).attr("query_string");
      						$.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type: 'GET',
                    url: '{{ route('get-post', $info['project_name']) }}',
                    data: {'vkid' : {{ session('vkid') }}, 'mode' : mode, 'author_id' : elem, 'query_string' : query_string},
                    beforeSend: function () {
                              _this.find('.icon').addClass('d-none');
                              _this.find('.icon').removeClass('d-inline-block');
                              _this.find('.spinner-border-sm').removeClass('d-none');
                    },
      							success: function(data){
                      if (data.success) {
                          _this.find('.icon').removeClass('d-none');
                          _this.find('.icon').addClass('d-inline-block');
                          _this.find('.spinner-border-sm').addClass('d-none');
        								$("#posts").html(data.html);
        								window.location = "#posts";
        							} else {
                        $('.toast-header').addClass('bg-danger');
                        $('.toast-header').removeClass('bg-success');
                        $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                        $('.toast').toast('show');
                      }}
      						});
      					});


            	} );
          </script>

          <script type="text/javascript">
              $(document).ready( function () {
                	$("#all_checkbox").click(function(){
                		if ($(this).is(":checked")){
                			$("#controls-checkbox input:checkbox").prop("checked", true);
                		} else {
                			$("#controls-checkbox input:checkbox").prop("checked", false);
                		}
                	});

                    $("#table-active").DataTable({
                		"language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Russian.json"
                        },
                		"lengthMenu": [ 40, 100, 200, 500 ],
                		"pageLength": 40,
                		"columnDefs": [ {
                		"targets": 1,
                		"orderable": false
                		} ],
                		"autoWidth": false
                	});
            	} );
          </script>
      </div>
    </div>
  </div>

  <a id="begin" href="#"></a>
  <div class="mb-5" id="posts"></div>
</div>
