<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Application - Aakasha Bindu Agritech</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <a href="index.html">
                    <img src="./images/Logo.png" alt="Aakasha Bindu Agritech Logo" class="navbar-logo">
                </a>
            </div>
            <nav class="nav">
                <ul class="nav-list">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="index.html#products">Products</a></li>
                    <li><a href="team.html">Team</a></li>
                    <li><a href="index.html#careers">Careers</a></li>
                    <li><a href="gallery.html">Gallery</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
                <div class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

<!-- =================== YOUR ORIGINAL HEADER & UI REMAINS SAME =================== -->
<!-- I am keeping your structure unchanged. Only payment script replaced. -->

<section class="application-section">
<div class="container">
<div class="application-form-container">
<h2 class="form-main-title">Application Form</h2>

<form class="application-form" id="applicationForm" method="POST" action="process_application.php" enctype="multipart/form-data">

    <!-- KEEP ALL YOUR ORIGINAL FORM FIELDS HERE EXACTLY SAME -->
<h3 class="form-section-title">Personal Information</h3>
                    
                    <div class="form-group">
                        <label for="full_name">Full Name (as per SSC) *</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth (as per SSC) *</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" required>

                        </div>

                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" name="gender" required>
                                <option value="">-- Select --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email Id *</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone No *</label>
                            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" placeholder="Enter 10-digit phone number" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="father_name">Father Name</label>
                            <input type="text" id="father_name" name="father_name" required>
                        </div>

                        <div class="form-group">
                            <label for="aadhar">Aadhar No</label>
                            <input type="text" id="aadhar" name="aadhar" pattern="[0-9]{12}" placeholder="Enter 12-digit Aadhar number" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="caste">Caste</label>
                            <input type="text" id="caste" name="caste" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"  required></textarea>
                    </div>

                    <h3 class="form-section-title">Educational Qualifications</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ssc_year">SSC passed out Year</label>
                            <input type="number" id="ssc_year" name="ssc_year" min="1980" max="2025" required>
                        </div>

                        <div class="form-group">
                            <label for="ssc_percentage">SSC Percentage/CGPA</label>
                            <input type="text" id="ssc_percentage" name="ssc_percentage" placeholder="e.g., 85 or 8.5" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="inter_year">Intermediate passed out Year</label>
                            <input type="number" id="inter_year" name="inter_year" min="1980" max="2025" required>
                        </div>

                        <div class="form-group">
                            <label for="inter_percentage">Intermediate Percentage/CGPA</label>
                            <input type="text" id="inter_percentage" name="inter_percentage" placeholder="e.g., 85 or 8.5" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="degree_year">Degree passed out Year</label>
                            <input type="number" id="degree_year" name="degree_year" min="1980" max="2025" required>
                        </div>

                        <div class="form-group">
                            <label for="degree_percentage">Degree Percentage/CGPA</label>
                            <input type="text" id="degree_percentage" name="degree_percentage" placeholder="e.g., 85 or 8.5" required>
                        </div>
                    </div>

                    <h3 class="form-section-title">Application Details</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="position">Applying for *</label>
                            <select id="position" name="position" required>
                                <option value="">-- Select --</option>
                                <option value="MAFO">Mandal Agriculture Field Officer</option>
                                <option value="DAFO">Divisional Agriculture Field Officer</option>
                                <option value="RAFO">Regional Agriculture Field Officer</option>
                                <option value="ZAFO">Zonal Agriculture Field Officer</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="exam_center">Preferred Exam Center *</label>
                            <select id="exam_center" name="exam_center" required>
                                <option value="">-- Select --</option>
                                <option value="Adilabad">Adilabad</option>
                                <option value="Alluri Sitharama Raju">Alluri Sitharama Raju</option>
                                <option value="Anakapalli">Anakapalli</option>
                                <option value="Anantapur">Anantapur</option>
                                <option value="Ananthapuramu">Ananthapuramu</option>
                                <option value="Annamayya">Annamayya</option>
                                <option value="Bapatla">Bapatla</option>
                                <option value="Chittoor">Chittoor</option>
                                <option value="Dr. B. R. Ambedkar Konaseema">Dr. B. R. Ambedkar Konaseema</option>
                                <option value="East Godavari">East Godavari</option>
                                <option value="Eluru">Eluru</option>
                                <option value="Guntur">Guntur</option>
                                <option value="Hyderabad">Hyderabad</option>
                                <option value="Kakinada">Kakinada</option>
                                <option value="Karimnagar">Karimnagar</option>
                                <option value="Khammam">Khammam</option>
                                <option value="Krishna">Krishna</option>
                                <option value="Kurnool">Kurnool</option>
                                <option value="Madanapalle">Madanapalle</option>
                                <option value="Mahabubnagar">Mahabubnagar</option>
                                <option value="Markapuram">Markapuram</option>
                                <option value="Medak">Medak</option>
                                <option value="Nalgonda">Nalgonda</option>
                                <option value="Nandyal">Nandyal</option>
                                <option value="Nizamabad">Nizamabad</option>
                                <option value="NTR">NTR</option>
                                <option value="Palnadu">Palnadu</option>
                                <option value="Parvathipuram Manyam">Parvathipuram Manyam</option>
                                <option value="Prakasam">Prakasam</option>
                                <option value="Ranga Reddy">Ranga Reddy</option>
                                <option value="Sri Potti Sriramulu Nellore">Sri Potti Sriramulu Nellore</option>
                                <option value="Sri Sathya Sai">Sri Sathya Sai</option>
                                <option value="Srikakulam">Srikakulam</option>
                                <option value="Tirupati">Tirupati</option>
                                <option value="Visakhapatnam">Visakhapatnam</option>
                                <option value="Vizianagaram">Vizianagaram</option>
                                <option value="Warangal">Warangal</option>
                                <option value="West Godavari">West Godavari</option>
                                <option value="YSR Kadapa">YSR Kadapa</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="form-section-title">Upload Documents</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="photo">Upload Photo *</label>
                            <input type="file" id="photo" name="photo" accept="image/jpeg,image/jpg,image/png" required>
                            <small class="file-hint">Max size 1 MB, jpg/png only</small>
                        </div>

                        <div class="form-group">
                            <label for="signature">Upload Signature *</label>
                            <input type="file" id="signature" name="signature" accept="image/jpeg,image/jpg,image/png" required>
                            <small class="file-hint">Max size 1 MB, jpg/png only</small>
                        </div>
                    </div>
    <!-- (Not repeating full 800 lines to avoid unnecessary duplication of unchanged UI code) -->
    <!-- Paste your entire form content here exactly as you gave -->

    <div class="payment-info-box">
    <h4><i class="fas fa-credit-card"></i> Payment Instructions</h4>
    <p>Pay the Entrance Exam Fee online using the payment gateway below.</p>
    <strong>Exam Fee: ₹1,200/-</strong>

    <button type="button" id="payButton" class="btn btn-payment">
    <i class="fas fa-lock"></i> Pay ₹1,200/- Now
    </button>

    <div class="payment-status" id="paymentStatus" style="display:none;"></div>
    </div>

    <div class="form-group">
    <label>Transaction Id Number *</label>
    <input type="text" id="transaction_id" name="transaction_id" readonly required>
    </div>

    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
    Submit Application
    </button>

</form>
</div>
</div>
</section>

<!-- footer -->
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Aakasha Bindu Agritech</h3>
                    <p>Leading the way in organic pesticide innovation for sustainable farming. Protecting crops, preserving nature.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="index.html#products">Products</a></li>
                        <li><a href="team.html">Team</a></li>
                        <li><a href="index.html#careers">Careers</a></li>
                        <li><a href="gallery.html">Gallery</a></li>
                        <li><a href="videos.html">Videos</a></li>
                        <li><a href="contact.html">Contact</a></li>
                        <li><a href="exam_application.html">Exam Application</a></li>
                        <li><a href="download_application.html">Download Application</a></li>
                        <li><a href="download_hallticket.html">Download Hall Ticket</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-map-marker-alt"></i> F.No 202, Radha Residency, Near Dmart Rudrampeta, Anantapur - 515 004. A.P.</p>
                    <p><i class="fas fa-phone"></i> +91 93903 44299</p>
                    <p><i class="fas fa-envelope"></i> aakasabindhuagritech@gmail.com</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Aakasha Bindu Agritech. All rights reserved.</p>
            </div>
        </div>
    </footer>

<!-- =================== CCAvenue Integration Script =================== -->

<script>

document.getElementById("payButton").addEventListener("click", function(){

    let fullName = document.getElementById("full_name").value;
    let email = document.getElementById("email").value;
    let phone = document.getElementById("phone").value;
    let position = document.getElementById("position").value;

    if(!fullName || !email || !phone || !position){
        alert("Please fill required fields before payment.");
        return;
    }

    if(phone.length != 10){
        alert("Enter valid 10 digit mobile number.");
        return;
    }

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "ccavRequestHandler.php";

    form.innerHTML = `
        <input type="hidden" name="amount" value="1200">
        <input type="hidden" name="billing_name" value="${fullName}">
        <input type="hidden" name="billing_email" value="${email}">
        <input type="hidden" name="billing_tel" value="${phone}">
        <input type="hidden" name="position" value="${position}">
    `;

    document.body.appendChild(form);
    form.submit();
});


/* Handle return from CCAvenue */
const urlParams = new URLSearchParams(window.location.search);

if(urlParams.get("payment") === "success"){
    let tid = urlParams.get("tid");

    document.getElementById("transaction_id").value = tid;
    document.getElementById("submitBtn").disabled = false;
    document.getElementById("payButton").style.display = "none";

    alert("Payment Successful! Transaction ID: " + tid);
}

if(urlParams.get("payment") === "failed"){
    alert("Payment Failed. Please try again.");
}

</script>

</body>
</html>