
function renderCartItems(data) {
    let popupCart = document.getElementById("popup-cart"),
        rect = document.getElementById("cart-trigger").getBoundingClientRect(),
        left = rect.x - 80 + "px",
        top = rect.y + 48 + "px";

    popupCart.style.left = left;
    popupCart.style.top = top;
    popupCart.style.height = "0";

    if (popupCart.children.length == 0) {
        initializeCartItems();
    } else {
        toggleCart(true);
    }
 
    popupCart.onmouseover = () => renderCartItems(data);
    popupCart.onmouseout = () => toggleCart(false);
}

function initializeCartItems() {
    let popupCart = document.getElementById("popup-cart"),
        itemsSection = document.createElement("div");

    for (let itemData of data) {
        itemsSection.appendChild(buildCartItem(itemData));
    }
    itemsSection.id = "popup-cart-items-section";
    popupCart.append(itemsSection, buildTotalContainer(), buildButtonContainer());
}

function toggleCart(on) {
    let popupCart = document.getElementById("popup-cart");

    if (!data || data.length == 0) {
        popupCart.innerHTML = "";
        popupCart.appendChild(buildNoItemsContainer());
    } else if(popupCart.children.length == 0) {
        initializeCartItems();
    }

    if (on) {
        popupCart.style.height = `${data.length * 100 + 175}px`;
    } else {
        popupCart.style.height = "0px";
    }
}

function buildCartItem(itemData) {
    let cartItemContainer = document.createElement("div"),
        productContainer = document.createElement("div"),
        closeIcon = document.createElement("i"),
        elements = [closeIcon, productContainer, cartItemContainer],
        classes = ["fa fa-close close-icon", "product-container", "cart-item-container"],
        children = [[], [buildProductImage(itemData), buildProductInfo(itemData)], [productContainer, closeIcon]];

    closeIcon.onclick = () => deleteItem(itemData);
    for (let i = 0; i < elements.length; i++) {
        elements[i].className = classes[i];
        for (let child of children[i]) {
            elements[i].appendChild(child);
        }
    }
    return cartItemContainer;
}

async function deleteItem(itemData) {
    let grandParent = event.target.parentElement.parentElement,
        item = event.target.parentElement;

    data = data.filter((item) => item.id == itemData.id);
    grandParent.removeChild(item);

    let response = await fetch(`http://localhost/project/cart_data.php?delete=${itemData.id}`,{
        headers : { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
         }   
      });

      location.reload();
}

function buildProductInfo(itemData) {
    let container = document.createElement("div"),
        qty = document.createElement("span"),
        name = document.createElement("span"),
        price = document.createElement("div"),
        elements = [container, qty, name, price],
        classes = ["product-details", "qty", "ellipsis", "price"],
        texts = ["", `${itemData.qty} x `, itemData.product_name, `$ ${parseFloat(itemData.price).toFixed(2)}`];

    for (let i = 0; i < elements.length; i++) {
        elements[i].className = classes[i];
        elements[i].innerText = texts[i];
    }

    for (let i = 1; i < elements.length; i++) {
        container.appendChild(elements[i]);
    }
    return container;
}

function buildProductImage(itemData) {
    let img = document.createElement("img"),
        url = `img/product/${itemData.product_name.replace(/\s/g, "_").toLowerCase().trim()}.png`,
        alt = url.substr(4);

    img.src = url;
    img.alt = alt;

    return img;
}

function buildTotalContainer() {
    let container = document.createElement("div"),
        shippingRow = document.createElement("div"),
        divider = document.createElement("div"),
        totalRow = document.createElement("div"),
        shippingLabel = document.createElement("span"),
        shippingValue = document.createElement("span"),
        totalLabel = document.createElement("span"),
        totalValue = document.createElement("span"),
        rows = [shippingRow, divider, totalRow],
        rowClasses = ["shipping-row", "divider", "total-row"],
        rowChildren = [[shippingLabel, shippingValue], [], [totalLabel, totalValue]];

    totalLabel.innerText = "Total";
    totalValue.innerText = "$ 0.00";
    shippingLabel.innerText = "Shipping";
    shippingValue.innerText = "Free shipping!";

    for (let i = 0; i < rows.length; i++) {
        let row = rows[i];
        row.className = rowClasses[i];
        for (let child of rowChildren[i]) {
            row.appendChild(child);
        }
        container.appendChild(row);
    }
    container.className = "total-container";
    return container;
}

function buildButtonContainer() {
    let container = document.createElement("div"),
        btn = document.createElement("button");

    container.id = "button-container";
    btn.type = "button";
    btn.innerText = "CHECK OUT";

    container.appendChild(btn);

    return container;
}

function buildNoItemsContainer() {
    let popoutCart = document.getElementById('popup-cart'),
        msg = document.createElement("span");

    msg.innerText = "No items in cart!";
    msg.style.margin = "auto"
    popoutCart.appendChild(msg);
    return msg;
}
