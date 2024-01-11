<?php
require_once "db_connector.php";

$conn=connect2db();

$sql="select distinct building as name from classroom";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result

foreach ($result as $building){

    $sql="select name, id from classroom where building=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $building['name']);
    $stmt->execute();

    $classrooms = $stmt->get_result(); // get the mysqli result
    $buildings[$building['name']]=array();
    while ($classroom = $classrooms->fetch_assoc()) {
        array_push($buildings[$building['name']], $classroom);
    }
}
$conn->close();
//print_r($buildings);
?>

<nav class="sidebar">
    <div class="sidebar-header text-center">
        <img src="img/img-logo.png" alt="aegean" class="img-fluid mx-auto d-block object-fit-cover w-100 rounded-3">
        <a href="index-GR.php" class="text-wrap text-capitalize text-center">Προβολή Αιθουσών</a>
    </div>

    <ul class="list-unstyled components">
        <?php
        foreach ($buildings as $building_name=>$classes){
            $id=str_replace(' ', '', $building_name);
            ?>
            <li class="active pb-2">
                <a href="#<?php echo $id; ?>" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle text-capitalize"><strong><?php echo $building_name; ?></strong></a>
                <ul class="collapse list-unstyled" id='<?php echo $id; ?>'>
                    <?php foreach ($classes as $class){ ?>
                        <li class="sidebar-li px-2">
                            <form action="create_event-GR.php" method="post">
                                <button class="btn" name="classroom" value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></button>
                            </form>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
    </ul>
</nav>
