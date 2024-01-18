// -------------------------------------------------- timer button -------------------------------------------------- 
function startTimer() {
    // Set the countdown time to 10 minutes (600 seconds)
    var mins = 10; // minutes to countdown from
    var countdownTime = mins * 60;

    // Update the timer every second
    var timerInterval = setInterval(function () {
        countdownTime--;

        // Display the remaining time on the button
        document.querySelector('.timerBtn').innerHTML = `<i class="fas fa-sync-alt"></i> (${formatTime(countdownTime)})`;

        // Check if the countdown has reached 0
        if (countdownTime <= 0) {
            clearInterval(timerInterval); // Stop the timer
            alert('Time to logout!'); // Display the alert message
            window.location.href = 'navbar-EN.php?logout=1';
        }
    }, 1000);
}

function formatTime(seconds) {
    // Format the time as MM:SS
    var minutes = Math.floor(seconds / 60);
    var remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
}

function clearEventForm() {
    var classID = document.getElementById("classID").value;
    var startDate = document.getElementById("startDate").value;
    var className = document.getElementById("className").value;

    document.getElementById("create_event_form").reset();

    document.getElementById("classID").value = classID;
    document.getElementById("startDate").value = startDate;
    document.getElementById("className").value = className;
}

function clearEventFileForm() {
    document.getElementById("create_event_from_file_form").reset();
}

function handleLabTheorySwitch(checkBoxID, hiddenElementID) {
    var checkbox = document.getElementById(checkBoxID);
    var element = document.getElementById(hiddenElementID);

    // Attach an event listener to the checkbox's change event
    checkbox.addEventListener('change', function () {
        // If the checkbox is checked, remove the 'visually-hidden' class
        // If the checkbox is not checked, add the 'visually-hidden' class

        if (checkbox.checked) element.classList.remove('visually-hidden')
        else element.classList.add('visually-hidden')

    });
}

function clearClassForm() {
    document.getElementById("create_class_form").reset();
}

function clearRecoupmentForm() {
    document.getElementById("recoupment_form").reset();
}

function createSuccessAlert(title = 'Success!',text='') {
    return Swal.fire({
        position: 'center',
        icon: 'success',
        title: title,
        showConfirmButton: false,
        text: text,
        timer: 4000
    });
}
function createErrorAlert(title ='Oops...',text='Something went wrong!') {

    return Swal.fire({
        position: 'center',
        icon: 'error',
        title: title,
        text: text,
        showConfirmButton: false,
        timer: 4000
    })
}
function createWarningAlert(){
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
    icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true
}).then(function(result) {
        if (result.value) {
            Swal.fire(
                "Deleted!",
                "Your file has been deleted.",
                "success"
            )
            // result.dismiss can be "cancel", "overlay",
            // "close", and "timer"
        } else if (result.dismiss === "cancel") {
            Swal.fire(
                "Cancelled",
                "Your imaginary file is safe :)",
                "error"
            )
        }
    });
}
// -------------------------------------------------- /timer button -------------------------------------------------- 

// -------------------------------------------------- MAIN --------------------------------------------------
// Start the timer when the page loads
window.onload = startTimer();
