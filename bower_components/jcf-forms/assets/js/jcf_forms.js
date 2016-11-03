document.body.addEventListener('keyup', function (e) {
    var element = e.target;

    if (element.tagName.toLowerCase() == 'input') {
        if(determineFloat(element)) {
            element.classList.add('visited');
        }
    }
});

function determineFloat(input) {
    if(input.value.length === 0) {
        input.classList.remove('active');
        return false;
    }
    input.classList.add('active');
    return true;
}

function checkAutoFill(){

    setTimeout(function() {

        var inputs = document.querySelectorAll('input');

        for (i = 0; i < inputs.length; i++) {
            if(determineFloat(inputs[i])) {
                inputs[i].classList.add('visited');
            }
        }
        checkAutoFill();
    }, 100);
}

checkAutoFill();
