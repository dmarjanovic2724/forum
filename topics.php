<?php
require_once "conn.php";
session_start();
$userId = $_SESSION['userId'];
require_once "commponents/head.php";
if (empty($_SESSION['userId'])) {
    header("Location: index.php");
}
if(!isset($_SESSION['cat'])){
    $cat_id = $_GET['cat'];
    $_SESSION['cat']=$cat_id;
    }else{
        $cat_id=$_SESSION['cat'];
    }
$query = "SELECT * from categories
WHERE id = $cat_id";
$result = $conn->query($query);
$cat = $result->fetch_assoc();
$catName = $cat['cat_name'];
$catDescription = $cat['cat_description'];
$date = date('Y-m-d H:i:s');
$message="";

if ($_SERVER["REQUEST_METHOD"] == "POST") {    

    if(empty($_POST['topicName'])){
        $validated=true;        
        $message="enter a topic name";
    }else{
        $topicName =$conn->real_escape_string($_POST['topicName']);
        $validate = false;
    }

    if ($validate == false) {
        $query = "INSERT INTO topics (topic_name, topic_date, cat_id, user_id) VALUES
        ('$topicName', '$date', $cat_id, '$userId')";
        $result = $conn->query($query);
        if ($result) {            
            $message = "new topic has been created";
            header("refresh:2;url=topics.php?cat=$cat_id");           
        } else {
            $message = "something went wrong, the topic isn't created";
            header("refresh:2;url=topics.php?cat=$cat_id");
        }
    }
}
?>
<div class="topic-header">
    <div class="topic-name"><h1><?php echo $catName ?></h1></div>
    <div class="topic-description"><h5><?php echo $catDescription ?></h5></div>
</div>


<div class="form-post">
    <form action="#" method="POST">
            <input type="text" name="topicName" value="" placeholder="Create new topic...">
            <input type="submit" name="submit" value="Create"><span class="errors">
            <p class="errors"><?php echo $message ?></p>   
    </form>
</div>



<?php

$query="SELECT topics.id ,topics.topic_name, topics.topic_date, topics.topic_done, categories.cat_name, users.username AS 'created_by' FROM users INNER JOIN topics ON topics.user_id = users.id INNER JOIN categories ON topics.cat_id = categories.id WHERE cat_id = $cat_id";
$result=$conn->query($query);

if($result->num_rows !=0)
{


    echo "<table class='table'>
            <th>topic name:</th>
            <th>topic date:</th>
            <th>Created by:</th>
            <th>cat name</th>
            <th>STATUS</th>";
    foreach ($result as $topic) {
        $id=$topic['id'];
        $_SESSION['cat_id']=$id;
        $status=$topic['topic_done'];
       
        echo "<tr>";
        echo "<td><a href='posts.php?id=$id'>".$topic['topic_name']."</a></td>";
        echo "<td>".$topic['topic_date']."</td>";
        echo "<td>".$topic['created_by']."</td>";
        echo "<td>".$topic['cat_name']."</td>";
        if($status ==1){
            echo "<td>CLOSED</td>";
        }else{
            echo "<td>ACTIVE</td>";
        }    
        echo "</tr>";
    }
    echo "</table>";
}
include_once "commponents/footer.php";
?>


