<?php
require_once "db_connector.php";

$conn=connect2db();

$sql="select distinct building as name from classroom";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result

foreach ($result as $building){

    $sql="select name from classroom where building=?";
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
        <h3>Sidebar</h3>
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
                <li><a href="#"><?php echo $class['name']; ?></a></li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
    </ul>
</nav>