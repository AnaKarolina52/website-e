

$(function (){
    const $basketQuantity = $('#basket-quantity');
    const $addToBasket = $('.btn-add-to-basket');
    const $itemQuantities = $('.item-quantity');
    $addToBasket.click(ev =>{
        ev.preventDefault();
        const $this = $(ev.target);
        const id = $this.closest('.product-item').data('key');
        console.log(id);
        $.ajax({
            method:'POST',
            url: $this.attr('href'),
            data: {id},
            success: function (){
                console.log(arguments)
                $basketQuantity.text(parseInt($basketQuantity.text() || 0) + 1);

            }
        })
    })

    $itemQuantities.change(ev => {
        const $this= $(ev.target);
        let $tr= $this.closest('tr');
        const $td = $this.closest('td');
        const id = $tr.data('id');

        $.ajax({
            method: 'post',
            url: $tr.data('url'),
            data: {id, quantity: $this.val()},
            success: function (result){
                $basketQuantity.text(result.quantity);
                $td.next().text(result.price);


            }
        })
    })
})