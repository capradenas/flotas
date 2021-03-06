//window.dt = require( 'datatables.net-dt' )(window, window.$);

  $(document).ready(function () {




    const _raw_data = JSON.parse($("#__data").val());

    const _prep_data = _raw_data.map((e)=>{
        return [
          e.id,
          `<span class="badge patente black">${e.patente}</span>`,
          e.marca,
          e.modelo,
          e.anio,
          e.kilometrajeInicial,
          e.dueno
        ]
    });

    $('#table-custom-elements').DataTable({
      responsive: true,
      data: _prep_data,
      columnDefs: [
        {
          targets: 0,
          searchable: !1,
          orderable: !1,
          className: 'dataTables-checkbox-column',
          render: function (t, e, i, n) {

              return `<div class="valign-wrapper">
                        <a href="/vehiculo/${t}" title="Ficha Vehículo" class="desc-icon"><i class="lite material-icons">directions_bus</i></a>
                        <a href="/vehiculo/${t}/seguro" title="Seguros" class="desc-icon"><i class="lite material-icons">card_travel</i></a>
                        <a href="/vehiculo/${t}/combustible" title="Combustibles" class="desc-icon"><i class="lite material-icons">local_gas_station</i></a>
                        <a href="/vehiculo/${t}/configuracion" title="Configuraciones" class="desc-icon"><i class="lite material-icons">settings</i></a>
                      </div>`;
          }
        }
      ],
      language: {
        search: 'Busqueda',
        searchPlaceholder: 'Ingresa un criterio de Búsqueda'
      },
      order: [
        1,
        'asc'
      ],
      dom: 'ft<"footer-wrapper"l<"paging-info"ip>>',
      scrollY: '400px',
      scrollCollapse: !0,
      pagingType: 'full',
      drawCallback: function (t) {
        var e = this.api();
        $(e.table().container()).find('.paginate_button').addClass('waves-effect'),
        e.table().columns.adjust()
      }
    });
    $('#table-custom-elements').on('change', 'input[type=checkbox]', function (t) {
      var e = $(this).parentsUntil('table').closest('tr');
      e.toggleClass('selected', this.checked)
    }),
    $('#table-custom-elements .select-all').on('click', function () {
      var t = e.rows({
        search: 'applied'
      }).nodes();
      $('input[type="checkbox"]', t).prop('checked', this.checked),
      $(t).toggleClass('selected', this.checked)
    })


    $('.desc-icon').on({
      'mouseenter': function(event){
          $(this).find('.material-icons').removeClass('lite').addClass('small');
      },
      'mouseleave': function(event){
        $(this).find('.material-icons').removeClass('small').addClass('lite');
      }
    });

  });
