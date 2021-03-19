

$(function (){
    const $basketQuantity = $('#basket-quantity');
    const $addToBasket = $('.btn-add-to-basket');
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
})