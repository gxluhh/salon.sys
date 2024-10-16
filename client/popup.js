function openPopup() {
    document.querySelector('#bookpopup').classList.add('show');
    document.querySelector('#overlay').classList.add('show');
}

document.querySelector('#close').addEventListener('click', function () {
    document.querySelector('#bookpopup').classList.remove('show');
    document.querySelector('#overlay').classList.remove('show');
    document.querySelector('#customerNum').classList.remove('show');
});

function popup() {
    document.querySelector('#customerNum').classList.add('show');
    document.querySelector('#overlay').classList.add('show');
}

function successclose() {
    document.querySelector('#success-container').classList.add('hide');
    document.querySelector('#error-container').classList.add('hide');
}

function next() {
    alert("Appointment Added !");
}

function done() {
    alert("Booked successfully !");
}