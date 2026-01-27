<?php
session_start();
if(!isset($_SESSION['examiner_logged_in'])){
    header("Location: exam_login.html");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Enter Marks</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="exam_marks_styles.css">
</head>
<body>

<div class="container">
<div class="card">
<h2>Enter Marks</h2>

<div class="msg" id="msg"></div>

<form id="marksForm">

<div class="row">
    <input type="text" id="application_id" name="application_id" placeholder="Application ID" required>
    <button type="button" id="validateBtn">Validate</button>
</div>

<input type="number" name="s1" placeholder="Subject 1 Marks" disabled required>
<input type="number" name="s2" placeholder="Subject 2 Marks" disabled required>
<input type="number" name="s3" placeholder="Subject 3 Marks" disabled required>

<button id="submitBtn" disabled>Save Marks</button>

</form>
</div>
</div>

<script>
const validateBtn=document.getElementById("validateBtn");
const submitBtn=document.getElementById("submitBtn");
const msg=document.getElementById("msg");
const marks=document.querySelectorAll("input[name='s1'],input[name='s2'],input[name='s3']");

validateBtn.onclick=()=>{
    msg.textContent="";
    marks.forEach(i=>i.disabled=true);
    submitBtn.disabled=true;

    const id=document.getElementById("application_id").value.trim();
    if(!id){ msg.textContent="Enter Application ID"; msg.className="msg error"; return; }

    fetch("validate_application.php",{
        method:"POST",
        body:new URLSearchParams({application_id:id})
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.success){
            msg.textContent="Application ID Valid";
            msg.className="msg success";
            marks.forEach(i=>i.disabled=false);
            submitBtn.disabled=false;
        }else{
            msg.textContent=d.message;
            msg.className="msg error";
        }
    });
};

document.getElementById("marksForm").onsubmit=e=>{
    e.preventDefault();
    submitBtn.disabled=true;

    fetch("save_marks.php",{method:"POST",body:new FormData(e.target)})
    .then(r=>r.json())
    .then(d=>{
        msg.textContent=d.message;
        msg.className=d.success?"msg success":"msg error";
        if(d.success){
            e.target.reset();
            marks.forEach(i=>i.disabled=true);
        }else submitBtn.disabled=false;
    });
};
</script>

</body>
</html>
