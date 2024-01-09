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
        }
    }, 1000);
}

function formatTime(seconds) {
    // Format the time as MM:SS
    var minutes = Math.floor(seconds / 60);
    var remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
}
function clearForm() {
    var classID = document.getElementById("classID").value;
    var startDate = document.getElementById("startDate").value;
    var className = document.getElementById("className").value;
    
    document.getElementById("create_event_form").reset();

    document.getElementById("classID").value = classID;
    document.getElementById("startDate").value = startDate;
    document.getElementById("className").value = className;
}
// -------------------------------------------------- /timer button -------------------------------------------------- 

// -------------------------------------------------- MAIN --------------------------------------------------
// Start the timer when the page loads
window.onload = startTimer();
