function create_table() {
  if (products && "null".localeCompare(products) != 0) {
    if (products.length > 0) {
      console.log(products);
      insert_thead();
      insert_tfoot();
      insert_tbody();
      insert_navigation(isLogged);
    }else {
      document.getElementById("empty-alert").innerText = "Cart is empty";
    }
  } else {
    document.getElementById("empty-alert").innerText = "Cart is empty";
  }
}

//Creats the header of the table (doesn't change unless the cart is epty)
function insert_thead() {
  let thead = document.getElementsByTagName("thead");
  thead = thead[0];

  let row = thead.insertRow(0);

  let cell1 = row.insertCell(0);
  let cell2 = row.insertCell(1);
  let cell3 = row.insertCell(2);
  let cell4 = row.insertCell(3);
  let cell5 = row.insertCell(4);
  let cell6 = row.insertCell(5);
  let cell7 = row.insertCell(6);

  cell1.innerHTML = "Product";
  cell2.innerHTML = "Description";
  cell3.innerHTML = "Availability";
  cell4.innerHTML = "Unit price";
  cell5.innerHTML = "Qty";
  cell6.innerHTML = "Total";
}

//creats the footer of the table (contains the total sum only)
function insert_tfoot() {
  let tfoot = document.getElementsByTagName("tfoot");
  tfoot = tfoot[0];

  let row1 = tfoot.insertRow(0);
  let row2 = tfoot.insertRow(1);

  let couponCell = row1.insertCell(0);
  let productsTotalCell = row1.insertCell(1);
  let productsPriceCell = row1.insertCell(2);

  couponCell.setAttribute("colspan", "3");
  couponCell.setAttribute("rowspan", "3");
  couponCell.appendChild(set_voucher());

  productsTotalCell.setAttribute("colspan", "2");
  productsTotalCell.innerHTML = "Total products";

  let totalProducts = calculate_total();

  productsPriceCell.setAttribute("colspan", "2");
  productsPriceCell.innerHTML = "$" + totalProducts;

  let cell1 = row2.insertCell(0);
  let cell2 = row2.insertCell(1);

  cell1.setAttribute("colspan", "2");
  cell1.innerHTML = "TOTAL";

  let total = calculate_total();

  cell2.setAttribute("colspan", "2");
  cell2.innerHTML = "$" + total;
}

//Creates the body of the table
function insert_tbody() {
  let tbody = document.getElementsByTagName("tbody");
  tbody = tbody[0];

  let products_counter = 0;

  //loop over the items and add them to the body
  products.forEach((product) => {
    let row = tbody.insertRow(products_counter);
    insert_product(product, row);

    products_counter++;
  });
}

//inserts the provided product (item) into the body provided table row
function insert_product(product, row) {
  let cell1 = row.insertCell(0);
  let cell2 = row.insertCell(1);
  let cell3 = row.insertCell(2);
  let cell4 = row.insertCell(3);
  let cell5 = row.insertCell(4);
  let cell6 = row.insertCell(5);
  let cell7 = row.insertCell(6);

  let url = `img/product/${product.product_name
    .replace(/\s/g, "_")
    .toLowerCase()
    .trim()}.png`;

  cell1.className = "cart_product";
  cell1.innerHTML = '<img src="' + url + '">';

  //print the description and make it a link
  let a = document.createElement("a");
  let desc = document.createTextNode(product.product_name);
  a.appendChild(desc);

  a.href = `http://localhost/project/?product_page.php?id=${product.product_id}`; //Link to the single item view page is added here
  cell2.appendChild(a);

  if (parseInt(product.quantity) >= parseInt(product.qty)) {
    cell3.appendChild(set_availability(true));
  } else {
    cell3.appendChild(set_availability(false));
  }
  cell3.className = "availability";

  cell4.innerHTML = product.price;
  cell5.appendChild(set_quantity(product.id, product.qty));

  if (product.updated_price == 0) {
    cell6.innerHTML = calculate_unit_total(product.qty, product.price);
  } else {
    cell6.className = "cart-product-price";

    let a1 = document.createElement("a");
    let a2 = document.createElement("a");

    a1.className = "cancelled-price";
    a2.className = "new-price";

    a1.innerHTML = calculate_unit_total(product.qty, product.price);
    a2.innerHTML = calculate_unit_total(product.qty, product.updated_price);
    cell6.appendChild(a1);
    cell6.appendChild(a2);
  }

  cell7.appendChild(set_delete_button(product.id, product.product_id));
}

//sets the cell in the availability column to (in stock)/(out of stock)
function set_availability(isAvailable) {
  let a = document.createElement("a");
  let avl;

  if (isAvailable) {
    avl = document.createTextNode("In\nStock");
    a.setAttribute("class", "available");
  } else {
    avl = document.createTextNode("Out of Stock");
    a.setAttribute("class", "unavailable");
  }
  a.appendChild(avl);

  return a;
}

//provides a quantity selector with a default quantity (selected from the db)
function set_quantity(id, qty) {
  let input = document.createElement("input");
  input.style = "qty-selector";
  input.type = "number";
  input.name = "Qty";
  input.min = 1;
  input.defaultValue = qty;
  input.className = "qty_selector";

  input.onchange = async () => {
    let response = await fetch(
      `http://localhost/project/cart_data.php?product_id=${id}&qty=${input.value}`,
      {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      }
    );
    console.log(id);

    location.reload();
  };

  return input;
}

function set_delete_button(id, prdId) {
  let delete_button = document.createElement("i");
  delete_button.className = "fa fa-trash dlt_button";

  delete_button.onclick = async () => {
    let response = await fetch(
      `http://localhost/project/cart_data.php?delete=${id}`,
      {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      }
    );

    location.reload();
  };

  return delete_button;
}

function set_voucher() {
  let fieldset = document.createElement("fieldset");
  let h3 = document.createElement("h3");
  let input = document.createElement("input");
  let button = document.createElement("button");
  let span = document.createElement("span");

  fieldset.className = "voucher_fieldset";

  h3.innerText = "VOUCHERS";
  fieldset.appendChild(h3);

  input.className = "voucher_input";
  input.type = "text";
  fieldset.appendChild(input);

  span.innerText = "OK";
  span.className = "voucher_span";

  button.type = "submit";
  button.className = "voucher_btn";
  button.appendChild(span);
  fieldset.appendChild(button);

  button.onclick = async () => {
    let response = await fetch(
        `http://localhost/project/cart_data.php?voucher_code=${input.value}`,
        {
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
          },
        }
      );
      console.log(response);

    location.reload();
  };
  return fieldset;
}

function calculate_total() {
  let total = 0;
  products.forEach((product) => {
    if (product.updated_price == 0) {
      if (parseInt(product.quantity) >= parseInt(product.qty)) {
        total += calculate_unit_total(product.qty, product.price);
      }
    } else {
      if (parseInt(product.quantity) >= parseInt(product.qty)) {
        total += calculate_unit_total(product.qty, product.price);
      }
    }
  });
  return total.toFixed(2);
}

function calculate_unit_total(qty, price) {
  return qty * price;
}

function insert_navigation(isLogged) {
  let div = document.getElementById("cart_page_body");
  let p = document.createElement("p");
  let a1 = document.createElement("a");
  let a2 = document.createElement("a");

  a1.id = "to_home";
  a1.innerHTML = "< Continue shopping";
  a1.href = "http://localhost/project/index.php";
  p.appendChild(a1);

  a2.className = "to_checkout";
  a2.innerHTML = "PROCEED TO CHECKOUT >";

  if(isLogged == 1){
    a2.href = "http://localhost/project/checkout.php";
  }else{
    a2.href = "http://localhost/project/login.php?checkout=1";
  }
  p.appendChild(a2);

  p.className = "cart_navigation";
  div.appendChild(p);
}
