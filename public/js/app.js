var bindTableRowClick = function() {
  $("table.table-hover tr").bind('click', function() {
    if ($(this).attr("detail-href")) {
      window.document.location = $(this).attr("detail-href");
    }
  });
};

$(document).ready(function() {
  // Add DataTable functionality to the list of repos.
  // Set default sort to stars descending.
  var table = $('.table').DataTable({
    "order": [[6,"desc"]]
  });
  // Bind click functionality on table rows each time the table is re-drawn (e.g., paginated).
  table.on("draw", function() {
    bindTableRowClick();
  });
  // Bind click functionality on table rows at the initial screen load.
  bindTableRowClick();
});