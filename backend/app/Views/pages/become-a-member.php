<div class="background-image" style="background-image: url(<?php echo BASEURL ?>assets/img/page_profile_img.jpg);margin-top: -50px;">
    <div class="profile-title text-center">Become a Member
    </div>
</div>
</div>

<section class="background-image space-extra" style="background-image: url(assets/img/22630829_6552436.jpg);" id="show_msg">
    <h2 class="_title_decorator_target" style="text-align:center;">Registration</h2>
    <div class="container">
        <?php $validation = \Config\Services::validation(); ?>
        <form action="<?= BASEURL; ?>/member_req" method="post" id="memberForm">
            <div class="row member_box">
            <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Enter Your Name">
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="form-control"><i class="fa fa-calendar-alt"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="aadhar_no">Aadhar No.</label>
                        <input type="number" id="aadhar_no" name="aadhar_no" class="form-control" placeholder="Enter Your Aadhar No">
                    </div>
                    <div class="form-group">
                        <label for="present_address">Present Address</label>
                        <input type="text" id="present_address" name="present_address" class="form-control" placeholder="Enter Your Present Address">
                    </div>
                    <div class="form-group">
                        <label for="last_address">Last Address (if Retired)</label>
                        <input type="text" id="last_address" name="last_address" class="form-control" placeholder="Enter Your Last Address">
                    </div>
                    <div class="form-group">
                        <label for="service_details">Service Details</label>
                        <input type="text" id="service_details" name="service_details" class="form-control" placeholder="Enter Your Service Details">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="father_name">Father Name</label>
                        <input type="text" id="father_name" name="father_name" class="form-control" placeholder="Enter Your Father Name">
                    </div>
                    <div class="form-group">
                        <label for="email_id">Email</label>
                        <input type="email" id="email_id" name="email_id" class="form-control" placeholder="Enter Your Email Id">
                        <div id="email_error" class="text-danger" style="display: none;padding: 10px 0;">Please enter a valid email address.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="college_name">What's App No</label>
                        <input type="number" id="" name="whatsapp_no" class="form-control" placeholder="Enter Your What's App No">
                    </div>
                    <div class="form-group">
                        <label for="permanent_address">Permanent Address</label>
                        <input type="text" id="permanent_address" name="permanent_address" class="form-control" placeholder="Enter Your Permanent Address">
                    </div>
                    
                    <div class="form-group">
                        <label for="current_address">Current Address (Working)</label>
                        <input type="text" id="current_address" name="current_address" class="form-control" placeholder="Enter Your Current Address">
                    </div>
                    <div class="form-group">
                        <label for="certification_no">Certification No</label>
                        <input type="text" id="certification_no" name="certification_no" class="form-control" placeholder="Enter Your Certification No">
                    </div>
                </div>
                <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4 payment_detail aligncenter">
                    <table cellpadding="10" cellspacing="0">
                        <tr>
                            <td>Registration Amount</td>
                            <td>:</td>
                            <td>&#x20B9; 200</td>
                        </tr>
                    </table>
                </div>
                <div class="form-group" style="text-align:center;margin:30px 0px">
                    <button type="submit" onclick="submitForm()" class="th-btn">Submit</button>
                </div>
            </div>
    </div>
    </div>
    </form>
    </div>
</section>

<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .hide-me {
        display: none;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 14px;
        /* Adjust font size as needed */
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>