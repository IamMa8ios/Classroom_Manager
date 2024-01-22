// -------------------------------------------------- timer button -------------------------------------------------- 
function startTimer() {
    // αντίστροφη μέτρηση 10 λεπτών (600 seconds)
    var mins = 10; // minutes to countdown from
    var countdownTime = mins * 60;

    // Update το timer κάθε sec
    var timerInterval = setInterval(function () {
        countdownTime--;

        // εμφάνιση ώρας που απομένει στο κουμπί
        document.querySelector('.timerBtn').innerHTML = `<i class="fas fa-sync-alt"></i> (${formatTime(countdownTime)})`;

        // Αν το timer πάει 0
        if (countdownTime <= 0) {
            clearInterval(timerInterval); // σταματάμε το timer
            window.location.href = 'navbar-EN.php?logout=1';
        }
    }, 1000);
}

function formatTime(seconds) {
    // Μορφοποίηση ώρας σε:  MM:SS
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
    // εμφανίζει ένα στοιχείο βάσει του αν είναι checked το στοιχείο με checkBoxID
    var checkbox = document.getElementById(checkBoxID);
    var element = document.getElementById(hiddenElementID);

    if (checkbox.checked) element.classList.remove('visually-hidden')
    else element.classList.add('visually-hidden')

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

// -------------------------------------------------- /timer button -------------------------------------------------- 

// -------------------------------------------------- MAIN --------------------------------------------------
// Start the timer when the page loads
window.onload = startTimer();
