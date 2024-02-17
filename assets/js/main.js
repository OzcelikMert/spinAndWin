$(function() {
  $(document).on("click", ".cancelModalButton", function() {
    $(this).closest(".modal").modal("toggle");
  })
});