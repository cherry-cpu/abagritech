<?php

require_once 'config.php';
$data=null;

if(isset($_POST['application_id'])){
    $pdo=new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,DB_PASS
    );
    $st=$pdo->prepare("SELECT * FROM exam_marks WHERE application_id=?");
    $st->execute([$_POST['application_id']]);
    $data=$st->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Result</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
<div class="card">
<h2>Check Result</h2>

<form method="post">
<input type="text" name="application_id" placeholder="Application ID" required>
<button>Check</button>
</form>

<?php if($data): ?>
<table style="width:100%;margin-top:15px;">
<tr><th>Sub1</th><th>Sub2</th><th>Sub3</th><th>Total</th><th>Result</th></tr>
<tr>
<td><?= $data['subject1'] ?></td>
<td><?= $data['subject2'] ?></td>
<td><?= $data['subject3'] ?></td>
<td><?= $data['total'] ?></td>
<td><?= $data['result'] ?></td>
</tr>
</table>
<?php elseif($_POST): ?>
<p class="error">No result found</p>
<?php endif; ?>
</div>
</div>

</body>
</html>
