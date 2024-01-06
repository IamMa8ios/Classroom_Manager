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

<nav id="sidebar">
    <div class="sidebar-header">
        <img src="img/aegean1.png" alt="aegean" class="img-fluid mx-auto d-block object-fit-cover w-100">
        <h3>View Class Schedule</h3>
    </div>

    <ul class="list-unstyled components">
        <?php
            foreach ($buildings as $building_name=>$classes){
                $id=str_replace(' ', '', $building_name);
        ?>
        <li class="active">
            <a href="#<?php echo $id; ?>" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><?php echo $building_name; ?></a>
            <ul class="collapse list-unstyled" id='<?php echo $id; ?>'>
                <?php foreach ($classes as $class){ ?>
                <li>
                    <form action="index.php" method="post">
                        <button class="btn btn-block" name="classroom" value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></button>
                    </form>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
    </ul>
</nav>