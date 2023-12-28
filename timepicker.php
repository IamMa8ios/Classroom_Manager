<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Booking</title>
</head>
<body>

<div class="container-fluid px-1 px-sm-4 py-5 mx-auto">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10 col-lg-9 col-xl-8">
            <div class="card border-0">
                <div class="row px-3">
                    <div class="col-sm-2"> <label class="text-grey mt-1 mb-3">Open Hours</label> </div>
                    <div class="col-sm-10 list">
                        <div class="mb-2 row justify-content-between px-3"> <select class="mb-2 mob">
                                <option value="opt1">Mon-Fri</option>
                                <option value="opt2">Sat-Sun</option>
                            </select>
                            <div class="mob"> <label class="text-grey mr-1">From</label> <input class="ml-1" type="time" name="from"> </div>
                            <div class="mob mb-2"> <label class="text-grey mr-4">To</label> <input class="ml-1" type="time" name="to"> </div>
                            <div class="mt-1 cancel fa fa-times text-danger"></div>
                        </div>
                    </div>
                </div>
                <div class="row px-3 mt-3">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <div class="row px-3">
                            <div class="fa fa-plus-circle text-success add"></div>
                            <p class="text-success ml-3 add">Add</p>
                        </div>
                    </div>
                </div>
                <div class="row px-3 mt-3 justify-content-center"> <button class="btn exit mr-2">Cancel</button> <button class="btn btn-success ml-2">Done</button> </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
