function getData(e){
    'use strict';

    // get the information from add button
    var id    = e.getAttribute('data-id');
    var title = e.getAttribute('data-title');
    var price = e.getAttribute('data-price');
    var store = e.getAttribute('data-store');


    // add disabled to add button
    e.classList.remove('btn-success');
    e.classList.add('btn-default');
    e.classList.add('disabled');

    // add to calculate table
    var table = document.getElementById('calculate-table');
    var row = table.insertRow(0);

    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);

      cell1.innerHTML = title;
      cell2.innerHTML = '<input type="hidden" name="ids[]" value="'+ id +'"><input type="number" data-id="'+ id +'" onchange="return changePrice(this)" min="1" max="'+ store +'" class="form-control" name="quantity[]" value="1">';
      cell3.innerHTML = price; cell3.id = "price-"+id; cell3.className = "prices";
      cell4.innerHTML = '<button type="button" data-id="'+ id +'" onclick="return removeData(this)" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>';


    getAllPrice();


    return false;
}


function removeData(e){
    'use strict';

    // remove from calculate
    e.parentNode.parentNode.remove(e.parentNode);
    var id = e.getAttribute('data-id');

    // get the add button
    var addButton = document.getElementById('product-'+id);


    // remove disabled to add button
    addButton.classList.add('btn-success');
    addButton.classList.remove('btn-default');
    addButton.classList.remove('disabled');

    getAllPrice();


    return false;
}

function changePrice(e) {
  'use strict';

  var id = e.getAttribute('data-id');

  var priceValue  = document.getElementById('price-'+id);
  var originPrice = document.getElementById('pp-'+id);

  priceValue.innerHTML = originPrice.innerHTML * e.value;

  getAllPrice();


  return false;
}

function getAllPrice() {
  'use strict';

  var allPrice = 0;
  var prices = document.getElementsByClassName('prices');

  for(var i=0;i<prices.length;i++){
    allPrice += parseInt(prices[i].innerText);
  }

  var totalPrice = document.getElementById('total-price');
  totalPrice.innerHTML = allPrice;
  return false;
}
