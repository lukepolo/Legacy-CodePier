var inputs = document.querySelectorAll('input');

for (i = 0; i < inputs.length; i++) {
    inputs[i].onkeyup = function() {
        if(determineFloat(this)) {
            this.classList.add('visited');   
        }
    };
}

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
        for (i = 0; i < inputs.length; i++) {
            if(determineFloat(inputs[i])) {
                inputs[i].classList.add('visited');   
            }
        }
        checkAutoFill();
    }, 100);
}

checkAutoFill();