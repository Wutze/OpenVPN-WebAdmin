





$(function () {
  "use strict";

  // Fetch all the details element.
  const details = document.querySelectorAll("details");

  // Add the onclick listeners.
  details.forEach((targetDetail) => {
    targetDetail.addEventListener("click", () => {
      // Close all the details that are not targetDetail.
      details.forEach((detail) => {
        if (detail !== targetDetail) {
          detail.removeAttribute("open");
        }
      });
    });
  });



  $table.on("click-row.bs.table", function(e, row, $tr) {

    // prints Clicked on: table table-hover, no matter if you click on row or detail-icon
    console.log("Clicked on: " + $(e.target).attr('class'), [e, row, $tr]);

    // In my real scenarion, trigger expands row with text detailFormatter..
    //$tr.find(">td>.detail-icon").trigger("click");
    // $tr.find(">td>.detail-icon").triggerHandler("click");
    if ($tr.next().is('tr.detail-view')) {
      $table.bootstrapTable('collapseRow', $tr.data('index'));
    } else {
      $table.bootstrapTable('expandRow', $tr.data('index'));
    }
  });



} );














function test_autoclose(){
// Fetch all the details element.
const details = document.querySelectorAll("details");

// Add the onclick listeners.
details.forEach((targetDetail) => {
  targetDetail.addEventListener("click", () => {
    // Close all the details that are not targetDetail.
    details.forEach((detail) => {
      if (detail !== targetDetail) {
        detail.removeAttribute("open");
      }
    });
  });
});


}



