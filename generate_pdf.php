<?php
ob_start();

require_once 'config.php';
require_once __DIR__ . '/tcpdf/tcpdf.php';

/* ===============================
   DB CONNECTION
   =============================== */
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit('Database connection failed');
}

/* ===============================
   VALIDATE REQUEST
   =============================== */
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_GET['transaction_id'])) {
    http_response_code(400);
    exit('Invalid request');
}

$transaction_id = trim($_GET['transaction_id']);

/* ===============================
   FETCH APPLICATION DATA
   =============================== */
$sql = "
SELECT 
    e.application_id,
    e.full_name,
    e.father_name,
    e.date_of_birth,
    e.gender,
    e.email,
    e.phone,
    e.age,
    e.aadhar,
    e.caste,
    e.address,
    e.ssc_year,
    e.ssc_percentage,
    e.inter_year,
    e.inter_percentage,
    e.degree_year,
    e.degree_percentage,
    e.position,
    e.exam_center,
    e.transaction_id,
    e.photo_path,
    e.signature_path
FROM exam_applications e
WHERE e.transaction_id = :transaction_id
LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':transaction_id', $transaction_id);
$stmt->execute();
$data = $stmt->fetch();

if (!$data) {
    http_response_code(404);
    exit('Application not found');
}

/* ===============================
   TCPDF SETUP
   =============================== */
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetTitle('Application Form');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

/* ===============================
   DETAILS TABLE
   =============================== */

$pdf->SetFont('helvetica', '', 11);
$html = '
<html>

<head>
    <meta charset="utf-8" />
    <style>
        .pdf24_ sup {
            vertical-align: baseline;
            position: relative;
            top: -0.4em;
        }

        .pdf24_ sub {
            vertical-align: baseline;
            position: relative;
            top: 0.4em;
        }

        .pdf24_ a:link {
            text-decoration: none;
        }

        .pdf24_ a:visited {
            text-decoration: none;
        }

        @media screen and (min-device-pixel-ratio:0),
        (-webkit-min-device-pixel-ratio:0),
        (min--moz-device-pixel-ratio: 0) {
            .pdf24_view {
                font-size: 10em;
                transform: scale(0.1);
                -moz-transform: scale(0.1);
                -webkit-transform: scale(0.1);
                -moz-transform-origin: top left;
                -webkit-transform-origin: top left;
            }
        }

        .pdf24_layer {}

        .pdf24_ie {
            font-size: 1pt;
        }

        .pdf24_ie body {
            font-size: 12em;
        }

        @media print {
            .pdf24_view {
                font-size: 1em;
                transform: scale(1);
            }
        }

        .pdf24_grlink {
            position: relative;
            width: 100%;
            height: 100%;
            z-index: 1000000;
        }

        .pdf24_01 {
            position: absolute;
            white-space: nowrap;
        }

        .pdf24_02 {
            font-size: 1em;
            line-height: 0.0em;
            width: 49.5em;
            height: 70.08334em;
            border-style: none;
            display: block;
            margin: 0em;
        }

        @supports(-ms-ime-align:auto) {
            .pdf24_02 {
                overflow: hidden;
            }
        }

        .pdf24_03 {
            position: relative;
        }

        .pdf24_04 {
            position: absolute;
            pointer-events: none;
            clip: rect(2.256668em, 47.29167em, 67.7775em, 2.333333em);
            width: 100%;
        }

        .pdf24_05 {
            position: relative;
            width: 49.5em;
        }

        .pdf24_06 {
            height: 7.008333em;
        }

        .pdf24_ie .pdf24_06 {
            height: 70.08334em;
        }

        @font-face {
            font-family: "NQKTOU+Poppins SemiBold";
            src: url("data:application/octet-stream;base64,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") format("woff");
        }

        .pdf24_07 {
            font-size: 1.125em;
            font-family: "NQKTOU+Poppins SemiBold";
            color: #2E7D32;
        }

        .pdf24_08 {
            line-height: 1.762em;
        }

        .pdf24_09 {
            letter-spacing: 0em;
        }

        .pdf24_ie .pdf24_09 {
            letter-spacing: 0px;
        }

        @font-face {
            font-family: "FUGEEG+Poppins Regular";
            src: url("data:application/octet-stream;base64,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") format("woff");
        }

        .pdf24_10 {
            font-size: 0.6875em;
            font-family: "FUGEEG+Poppins Regular";
            color: #555555;
        }

        @font-face {
            font-family: "NCKSWG+Poppins Bold";
            src: url("data:application/octet-stream;base64,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") format("woff");
        }

        .pdf24_11 {
            font-size: 0.6875em;
            font-family: "NCKSWG+Poppins Bold";
            color: #212529;
        }

        .pdf24_12 {
            font-size: 0.75em;
            font-family: "NCKSWG+Poppins Bold";
            color: #212529;
        }

        @font-face {
            font-family: "KBAAGR+Poppins SemiBold";
            src: url("data:application/octet-stream;base64,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") format("woff");
        }

        .pdf24_13 {
            font-size: 0.6875em;
            font-family: "KBAAGR+Poppins SemiBold";
            color: #212529;
        }

        @font-face {
            font-family: "CIFOIO+Poppins Regular";
            src: url("data:application/octet-stream;base64,d09GRgABAAAAABBgAAoAAAAAHFwAAQABAAAAAAAAAAAAAAAAAAAAAAAAAABPUy8yAAAA9AAAAE8AAABkWgx9YWNtYXAAAAFEAAAA/wAABuBhWHEJZ2x5ZgAAAkQAAAteAAAP6HcNAbpoZWFkAAANpAAAADAAAAA2Od+PA2hoZWEAAA3UAAAAHQAAACQH2ALgaG10eAAADfQAAAC6AAAA4HyhC/Fsb2NhAAAOsAAAAJsAAADkAAG31G1heHAAAA9MAAAAIAAAACAHnBE9bmFtZQAAD2wAAADlAAACIr/TmX9wb3N0AAAQVAAAAAwAAAAgAAMAAHjaY2BhDmacwMDKwMHUxRTBwMDgDaEZ4xiMGD2AfKAUHDAyIAHPEDd/hgMMCgxlLFL/FjGksOQzFSswMEwGyTGpMO0CUgoMLCAeABkaCzsAeNrtldlOAkEURA/DOBpUFBUXFtdRQURB2Z9UkGhcA8YH3ox7oqNx4Tv8Kn8LSz5invredPe96U5XuqqSBmwgqOES0IzVUafK/tE6BM4vtlXShsceA2yxyCqbZFVtkyPPDrsUKFKiTIUqNZ3b54A6DQ5pcsQxJ5xyxjkXXNKizRXX3HDLHfc88MgTz7zwKow33vngky++6fZ6wvUbz8/wfM1xMZchxBobUjYgJudxsOSAIIPSNs8KYeaIESchhtOMkGSMKJNie5gJIowyxTTr0mJW/LtSZYmZvkIpKZVjgWWpk9XbjGuMa4xrjGt8cY3u0delVPwz22dX3R/NRNBoAHjafVcJcBPXGX7vydgQB/CBLDCnLPnAsmVLq5Uvea37tmRZkg+tbGPZGLAwGNvgGoLxkXEbQgJhSkiakmBIEyZJQ5ujU1OSNITeTMfpEUohbUhJMumRdtIMCYdW/VeSD0Iaz3i1u2/3/e/93/H/iwiCP9K0ECEBSkEIJ/7idxGKfJ+44PgSQvAE+gKhBQilXIdTjPLg+D75PX8nk6bSJel5Z4NkxBspJiMwaotypIL8Aa2DR8VCBlPKNVi4bAmW5OTlU2vgUk2r8iQ5ySmSdDFJ5SZFAZV3c7F6e/2WsSXNi31ad12di3ZnNOCXyf3X1q039VRb+/RbAxXaNpfLTxXhxyCGGOa+Rd5GyQgtwhIspsX4Fvf3IM7mhvGfyYHIABl3IRJbCkPeR8tRLiwZYor45WTFVyPHdJpYmSWSLCHCZVmJZeGf9Dc3YVFYv2kPHXZuGpBjFOq9x/eys6Sp3uytqyWPPnBMVTWyw7hVGzwxiLd3e45ILJSzrdHuao4FjBIVuYqy+Ryp+UnzE6FU6tnAkAYGx3PAB37NxnjdRBjSGLZo7us7sKM43NLVt9A33eBzmr31rgeYse8UU2XdjvD4j14Zfrphm/+w1+IINlltTRCwgAeLXED3oMUIldGYSqeEEqGEzsTiAtzW09kZSOL+ixdPmvCHnNQ9OYkvcOOAtyb6T6Ihf0ErUT6iEMrIo1UzQCWnzMAVW3WZdGadkKAleC5T+zXqsNMZLmPUjE270+ns11oZvLTObKrz6g3e91rZ2tpA0IEf9mnUjRTVqK5sFGa2GXTtNN2uM2zI+JNez5QbTeVvmUxal0trsMBmGNgMDatKhXWhjHjCkiFftFQOy+GD8/SB3DG9Bx7s7X3wQC8+JrIYjBaRyGI0WERk9ZtvvnD63LnTjUN2+1BjYLfdvjsAbHFiK76Mj8C+ERaKaXyZ24GP1NZGo6gSRn4FI2No0SdIy1OfPxJUC4e1ZArSmgG3kmUYSM4vJxm2DhcUWTu4e4K9umvP96zfnpjAj/Zz75KpvsA+ffVolFcIvI9OwfsQMRNePcWSqYgFrjHKgeGbcMYrTiiBMQmNb77OvkGm7JFHyHY7BAdyk1HyKjxyD0K54hRJJpUpyU/F+GNuunzD7S23NtQPDz/xBHk14vgAb+COx+Ph78KsoMgySihOp059xv2QtdtjISuiN0gGOYeKEMqS41kNwqmcxHEHEfAZTuYTLFpDYvB/0MPg9gUWo85RpC83easqtxhDexd2LrBXV1hlNRUmwLXbmqJorFAYdFSBMntphrRWV9FUGvIUVFfReaUr0jJza/VUoDyRzvWwliVIhJBYIIllM062zIQhyDA++8w29qq7R+31nub2TdjtNscEXs39jUypg1XWbRncFdxlq2aM8e2SHeQMWgSzSWLY8JjsYLnDLIu3sUQW+SM5E3mHFMKzWfDsQxA7YRXUSkwe4m5s4W60X74cRyXyLGmOTypII6+geyGXMKVAIuAnXoQFaeyH2o/YEIZnyCsRJ0mK3CYLIrfg/DzRwO5k0c9JIykGqgC4GfF88rZIJ0T0ZfbKjIM2a3d2MLgybLHtMuJppZ+ifEqlj6L8Srx5n5kp6Yxc6SqtsY55q4OK0iBT06JQtDCzvLo2j1fXIqvgOr4IHSxiOe+7KEUcA7mMmtFxQt1ywhuvmOgEnH3BRmuVOy2UdX/Itcds3FPn+oauVe4uNbWn4ABevNrDlsndHeZRv3/EZBx0WBz5TS6Iy6deBelMB4+TQKSEWYhnAc2dwTMj/iOk8HRNrnqTmbt909KpNJRx3ONas1l/9ANnneHJSdyutBfSfgWZKqlTMp6MXA1NayLvaisqDddhw7LoTfJr4O7q2cxK5mdzboey4FPtfc/l96zucdoG9PoBmxNO85/ra38qiN86Gdy2scZo3Ovz7jUaajp72BNdCVY+FhN55uxGeHEnFg6keuzZ5uZn+y5d+ubDByeGh8mU/8lw+JgXGHP44OjY4ciVhPLOAxVTAREh771ikJ9YeIrj8CruATxItn/s+I+DnHEkaMvEAiJxjLXpGPiFf8FdBdqu6+fO4xrMcOfIFPcaNnAnuafhHUX0FkmDiiKLO2I8x8KvqKgyTM+WlRSJYaTL2EFZKnRO1WZb97cWNqX4zGZb50bR8mCrScs4U5rJ0GaFj2YCixektTorWNX2Lo3TVunQO3EVQ2u1aohdEv0cXwROye40jrz/5xsifk3TnZWC1iRTuUqjNVTXlimDVWxvEiuooRWVErqkyk5jJVu1SFarXK+SS6SS9DSxji40rw/WyVSFOSslwsVpaxllsXU9DxB3mwzNCks9v5HgsydM+pKw3gI9DRpBXebwKsjoAOiKl5ZfyevqkxrzPp93zFpTwh0VFGzsCZYqWqoZXlg18dKDfkxuAIxL4+VwpvqIIFJIKs1eIZGsKGLJXml2tpT/b45cIVLIUTbg7yAv8hTKTF+WVQWmDgULxE/RFPDhbE6hod7ptFjYg/gS91qBuNZchw2O/fsdENQarRC8QEZROTLPKQk2FzvGMywqowSQ3tj2eGhht8lQgctEybO6JnNDUJzJD55uUnXoleqktW20s7Gm19ImTnUrBXRB1ZpjBwff2IqPP9xqvbc5HD7ZPPbRIZOzqEJVaitQU2TUd7TD/UhAKZMpW9yOMbeV4f6amaZx9pePH+r/5dD9B3OYXM/Rzoc+noi8QbNqla6iRUkzvPVwn8b8by0qgW3EvO8OclbjeS6U9SXM8K5wdlA76N56aHFgaave0bayOwHihfm2SIp5U5x27NR3NSmrW/39pVpwR98+85W7DFLwOpHPWBOAF++z5p9Qwjh34ShY11kZ8JgsvtiBZVeyZabubFG3jsjpbifIUF3fyDZwv5v5JVK3TM5oKrUQqih6Ez2DdvHKz5pHmLoEWZJnmBJzsc8JBRlaeaeLJdqqOQ8zDrk2jgtDSzwa14YNLk3dUnDnDteQkS8KoUZlZTjUsbVc5dtoHmlIWL4fZl0KmecFOtfDwXwM4Sv8bKr/vddiG3a5hm2OEZeigVb5FEqvUlGvWAQG7xs1mUb8/lFznTyo1weLi4N6Q7A44Vdd4Fep8YoojlmWOJ10sdwx0FcHXz6xhjsPhvVbrIQX6GgDnhS8yJcnvtbiSe6TAE4XvHjrVFITwlEuugM9j4b54TKBJPP53mrbMN+dJkKVA268E2fdjRY+e1+g3mL1A0It8NhQXxyaQAP3NpG6CuUwA5RtIgVJQp3PzaSgelN4xXPtF7svhnx4OiHWeJRraNnd7KDS+TBPdFbMxGmrmseBWKBr3KcQaabeX7uj3qd/fb23a7ethnofo/bd9b6qFOdEVm1SfHXBx82Ql9SYAYK3xFPy02bt8scBgpuq1me4d4i0GXrZGB9JDjrBf3vM9rLj/BV+YfYaQ7+/kCwj70GngHiSxOvF7HdRCtQv+qVDHTVmo0PQlDSycdNQx3AIN3FrT48fNOkcfkvv7r5O77FBt1MH7WZh9DNygfws5pszHxPzGvdcYEFS8txHw/wx9bz7hfVabT3/T6xcOICPcGP5+Tk5BQU5/4KbHg8MbIcL/gYZ8HiMJo/HtD9iJmeKS+TyUkWxvITTwy1jvcfUUiIvLimBO7BTa/QLkJ2UhxvPwQNQxeOmE4rNbinXdlUB5jYfkUaueApLAHOs437jAcxxQgRQBKf4XhM+HvgSDwjgi9zb169DV2//h537OYzyrLhBPiJvxtWY9PVqxAXHW1qOt4eOB4PHQ7adBsOA3T5gMOy0pbAnu7pOsuyJTZtOsG7jsNe3N9a2DPNdL/TOuIjchtYL4fiXA8XbjjAneTVMWdTPWixtWzSKfig0+fiSIz/od5ao8/8H547HnAAAeNpjYGQAA1PJu9bx/DZfOZiZX4D4T7OWScLov2//HuFiZ9EEctkYmBiAOgBHdwwLeNpjYGRgYJH6twhIsv3/x8DAvI8BHVgAAHJRBMAAAAB42h2NvQ4BURSE585uFKgUJBLFKhAkIqIgsiQawq1phEa3pZ9C41nEAyj0CokHkOi8hp8KY4svkzP3O/dwDJgOclygzyw8bpQrsUSBa7TU+8xjKJrswuIJywyy3MEzB1gTAzhAgzP1J7FH0pmEWQrdACXOYR1XedZsxEhYVBlFRd7/T98ESDsP9P57bkTdFWVOtbMVR925o47L96PZsop4mHLNUu5Nbhs1vlBU3zNv3UvorY/UD6GzKPMAAHjaJcytkoEBGIbhm/Wz3w+6pDgDMzYpzEgCQXAGmuAAdEVTzex2Mxplg7azFCdAML6qCAr38M5c5Z3neeB1X+proxOketrqDumZEvhIq6yBxprrT5e3zFBrnSFbVFsr/UOupm89IN/SSD/wWdECgkj+gwmE9sIDRA25Gf1CbC7uyl481U5XKNTlbmGvGxSbOkKpqo6WSp5jGx9vAAABAAAAOBAABAAA/wD/AAIAAgAeAP8AAABkAAAA/wAeeNqVkbGKwkAURc9o4rIptBObde2jEkmInaCCkMaI/RYKIoqYoKTzg6ws94P2S3xxHxZBkLwLb87cuY9hGKDODUNehuaj51XhQ3b/XBX6UraEfWVbFCnXaDBXdvjmR6aM9SlOl4tyRe66KlfF/1W2hP+UbRxTV67RNi1lh5EJp9EsjmJ3kaTp7njuLDfb7LA66fL68LUb9D0vUK+n3mScl1twQ38YJuv9wGcqb50RS49xWZCQinYcOdNhyYYtGQdWnAq7MpNlsgF9PFFQyPUKuQnjp9w32VB+dyg9Yc2eAf4dKotdWgAAAHjaY2BmwAsAAH0ABA==") format("woff");
        }

        .pdf24_14 {
            font-size: 0.6875em;
            font-family: "CIFOIO+Poppins Regular";
            color: #212529;
        }

        body>div {
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3) !important;
            margin: 20px auto !important;
        }
    </style>
</head>

<body>
    <div class="pdf24_ pdf24_02">
        <div class="pdf24_03">
            <img style="position: absolute; left: 575px; z-index: 0; top: 150px;" height="100" width="100" src="$data['photo_path']" alt="" class="pdf24_04" />
            <img style="position: absolute; left: 575px; z-index: 0;; top: 250px;" height="100" width="100" src="$data['signature_path']" alt="" class="pdf24_04" />
        </div>
        <div class="pdf24_view">
            <div class="pdf24_05 pdf24_06">
                <div class="pdf24_01" style="left:17.9356em;top:5.459em;"><span class="pdf24_07 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Aakasha Bindu Agritech &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:20.9004em;top:7.268em;"><span class="pdf24_10 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Exam Application Form &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:35.9756em;top:9.518em;"><span class="pdf24_11 pdf24_08 pdf24_09">Photo
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:10.1563em;top:9.5721em;"><span class="pdf24_12 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Personal Details &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:11.768em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Transaction / &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:12.893em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">UTR No &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:15.7969em;top:11.768em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['transaction_id'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:21.875em;top:11.768em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Date of Birth &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:27.9531em;top:11.768em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['date_of_birth'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:15.0805em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Full Name (as &nbsp; </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:16.2055em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">per SSC) &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:15.7969em;top:15.0805em;"><span class="pdf24_14 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">$data['full_name'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:15.7969em;top:18.393em;"><span class="pdf24_14 pdf24_08 pdf24_09">$data['gender']
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:18.393em;"><span
                        class="pdf24_13 pdf24_08 pdf24_09">Gender &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:21.875em;top:18.393em;"><span class="pdf24_13 pdf24_08 pdf24_09">Caste
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:27.9531em;top:18.393em;"><span class="pdf24_14 pdf24_08 pdf24_09">$data['caste']
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:20.518em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Father Name &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:22.4555em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Phone No &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:24.3305em;"><span
                        class="pdf24_13 pdf24_08 pdf24_09">Address &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:15.7969em;top:20.518em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['father_name'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:15.7969em;top:22.4555em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['phone'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:21.875em;top:22.4555em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Email ID &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:27.9531em;top:22.4555em;"><a href="mailto:spittala45@gmail.com"
                        target="_blank"><span class="pdf24_14 pdf24_08 pdf24_09">$data['email'] &nbsp;
                        </span></a></div>
                <div class="pdf24_01" style="left:15.7969em;top:24.3305em;"><span class="pdf24_14 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">$data['address'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:27.3305em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Aadhar No &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:15.7969em;top:27.3305em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['aadhar'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:10.1563em;top:29.8846em;"><span class="pdf24_12 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Educational Qualifications &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:31.893em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">SSC Year &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:17.5156em;top:31.893em;"><span class="pdf24_14 pdf24_08 pdf24_09">$data['ssc_year']
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:17.5156em;top:33.768em;"><span class="pdf24_14 pdf24_08 pdf24_09">$data['inter_year']
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:25.3125em;top:31.893em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">SSC % / CGPA &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:33.1094em;top:31.893em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['ssc_percentage'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:33.1094em;top:33.768em;"><span class="pdf24_14 pdf24_08 pdf24_09">$data['inter_percentage']
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:33.768em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Intermediate Year &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:25.3125em;top:33.768em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Intermediate % / &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:25.3125em;top:34.893em;"><span class="pdf24_13 pdf24_08 pdf24_09">CGPA
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:36.768em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Degree Year &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:17.5156em;top:36.768em;"><span class="pdf24_14 pdf24_08 pdf24_09">$data['degree_year']
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:25.3125em;top:36.768em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Degree % / CGPA &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:33.1094em;top:36.768em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['degree_percentage'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:10.1563em;top:39.3221em;"><span class="pdf24_12 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Application Details &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:41.3305em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Applying For &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:17.5156em;top:41.3305em;"><span class="pdf24_14 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">$data['position'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:25.3125em;top:41.3305em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Exam Center &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:25.3125em;top:44.3305em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Submitted On &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:33.1094em;top:41.3305em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['exam_center'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:33.1094em;top:44.3305em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['created_at'] &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:44.3305em;"><span class="pdf24_13 pdf24_08 pdf24_09"
                        style="word-spacing:0em;">Transaction / UTR &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:9.7188em;top:45.393em;"><span class="pdf24_13 pdf24_08 pdf24_09">No
                        &nbsp;
                    </span></div>
                <div class="pdf24_01" style="left:17.5156em;top:44.3305em;"><span
                        class="pdf24_14 pdf24_08 pdf24_09">$data['transaction_id'] &nbsp;
                    </span></div>
            </div>
        </div>
    </div>
</body>

</html>';

$pdf->writeHTML($html);


/* ===============================
   OUTPUT
   =============================== */
ob_end_clean();
$pdf->Output('Application_Form_'.$transaction_id.'.pdf', 'D');
exit;
