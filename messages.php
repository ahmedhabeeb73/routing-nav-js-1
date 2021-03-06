<?php
$title = "messages";
require_once('./template/header.php');
require_once('./config/db.php');
require_once('./config/app.php');

// $query = "SELECT  *, messages.id as message_id , 
// services.id  as services_id FROM messages 
//  left join services on messages.service_id=services.id order by messages.id";
// $messages = $mysqli->query($query)->fetch_all(MYSQLI_ASSOC);

$stat = $mysqli->prepare("SELECT  *, messages.id as message_id , 
                          services.id  as services_id FROM messages 
                          left join services on messages.service_id=services.id order by messages.id");
$stat->execute();
$messages = $stat->get_result()->fetch_all(MYSQLI_ASSOC);


?>

<?php if (!isset($_GET['id'])) : ?>

  <table id="customers">
    <tr>
      <th>id</th>
      <th>services</th>
      <th>Name</th>
      <th>email</th>
      <th>file</th>
      <th>message</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($messages as $message) : ?>
      <tr>
        <td><?php echo $message['message_id'] ?></td>
        <td><?php echo $message['service_name'] ?></td>
        <td><?php echo $message['name'] ?></td>
        <td><?php echo $message['email'] ?></td>
        <td><?php echo $message['file'] ?></td>
        <td><?php echo $message['message'] ?></td>
        <td>
          <a href="?id=<?php echo $message['message_id'] ?>">View</a>
          <form onsubmit="return confirm('are you sure')" method="post" class="form-delete">
            <input type="hidden" name="message_id" value="<?php echo $message['message_id'] ?>">
            <button class="delete-btn">delete</button>
          </form>
        </td>

      </tr>
    <?php endforeach; ?>
  </table>
<?php else :

  $messageQuery = "SELECT * FROM messages 
left join services
on messages.service_id=services.id
where messages.id=" . $_GET['id'] . " limit 1";
  $message = $mysqli->query($messageQuery)->fetch_array(MYSQLI_ASSOC);
?>

  <div class="message">
    <h2>From: <?php echo $message['name'] ?></h2>
    <small><?php echo $message['email'] ?></small>
    <p>Msg: <?php echo $message['message'] ?></p>
    <div>
      <small>services</small>
      <p>
        <?php
        if (isset($message['service_name'])) {
          echo $message['service_name'];
        } else {
          echo "no services";
        }
        ?>
      </p>

      <?php if ($message['file']) : ?>
        <div>
          <a href="<?php echo $config['app_url'] . $config['upload_dir'] . $message['file'] ?>">Download file</a>
        </div>

      <?php else : ?>

      <?php endif ?>
    </div>
  </div>


<?php endif ?>


<!-- delete message -->
<?php
if (isset($_POST['message_id'])) {
  $stat=$mysqli->prepare("delete from messages where id= ?");
  $stat->bind_param('i',$messageId);
  $messageId=$_POST['message_id'];
  $stat->execute();



  if($_POST['file']){
    unlink($config['upload_dir'].$_POST['file']);
  }
  header('location:messages.php');
}
?>
<?php require_once('./template/footer.php') ?>