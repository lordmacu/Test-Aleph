$(document).ready(function () {
  $("table.table").each(function () {
    if (!$.fn.DataTable.isDataTable(this)) {
      $(this).DataTable({
        responsive: true,
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
        },
      });
    }
  });
});
