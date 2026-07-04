<?php $this->load->view('common/inc/top-header'); ?>

   
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');

section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100vh;
    padding: 0 0px;
    background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
    overflow: hidden;
}

.query-container {
    width: 450px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    animation: query-fadeIn 0.5s ease-in-out;
    margin-right: 20px;
}

.background-image {
    flex: 1;
    height: 100%;
    background: url('<?=base_url('assets/frontend/banner/query-photo.jpg')?>') no-repeat center center;
    background-size: cover;
    position: relative;
    margin-right: 20px;
}

.overlay-text {
    position: absolute;
    top: 50%;
    left: 36%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: #fff;
    background: rgba(0, 0, 0, 0.5);
    padding: 20px;
    border-radius: 15px;
}

.overlay-title {
    font-size: 32px;
    margin-bottom: 10px;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
}

.overlay-description {
    font-size: 18px;
    font-weight: 400;
    font-family: 'Poppins', sans-serif;
}

.query-h1 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 28px;
    font-weight: 500;
    color: #333;
    font-family: 'Poppins', sans-serif;
}

.query-label {
    font-size: 16px;
    color: #555;
    margin-bottom: 8px;
    display: block;
    font-family: 'Poppins', sans-serif;
}

.query-input, .query-select, .query-textarea {
    width: 100%;
    padding: 14px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 12px;
    font-size: 15px;
    color: #333;
    background-color: #f9f9f9;
    box-sizing: border-box;
    transition: border-color 0.3s ease-in-out, background-color 0.3s;
}

.query-input:focus, .query-select:focus, .query-textarea:focus {
    border-color: #fd9644;
    background-color: #fff;
    outline: none;
}

.query-button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(120deg, #ff7e5f 0%, #feb47b 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

.query-button:hover {
    background-color: #ff6347;
}

.query-alert {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 12px;
    text-align: center;
    font-size: 15px;
}

.query-alert-danger {
    background-color: #ffb3b3;
    color: #a94442;
}

.query-alert-success {
    background-color: #d4edda;
    color: #155724;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    section {
        flex-direction: column;
        padding: 20px;
    }

    .query-container {
        width: 100%;
        margin: 60px;
    }

    .background-image {
        display: none; /* Hide the image on smaller screens */
    }
}

    </style>
</head>
<body>
    <?php $this->load->view('common/inc/header'); ?>
    
    <section style="background-color:1#fafafa;">
       <div class="background-image">
        <div class="overlay-text">
            <h2 class="overlay-title">We're Here to Help</h2>
            <p class="overlay-description">
                Whether you are a student, an employer, or just have a question, our team is ready to assist you. 
                Please fill out the form with your query, and we will get back to you as soon as possible.
            </p>
        </div>
    </div>
        <div class="query-container">
        <h1 class="query-h1">Submit Your Query</h1>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="query-alert query-alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('success')): ?>
            <div class="query-alert query-alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <form id="queryForm" method="POST" action="<?php echo site_url('query/submit'); ?>">
            <label for="userType" class="query-label">I am a:</label>
            <select id="userType" name="userType" class="query-select" required>
                <option value="">Select User Type</option>
                <option value="student">Student</option>
                <option value="employer">Employer</option>
                <option value="other">Other</option>
            </select>

            <label for="name" class="query-label">Your Name:</label>
            <input type="text" id="name" name="name" class="query-input" required>

            <label for="email" class="query-label">Email:</label>
            <input type="email" id="email" name="email" class="query-input" required>

            <label for="query" class="query-label">Your Query:</label>
            <textarea id="query" name="query" rows="4" class="query-textarea" required></textarea>

            <button type="submit" class="query-button">Submit</button>
        </form>
    </div>
    </section>
	<?php $this->load->view('common/inc/loginPopup'); ?>
	<?php $this->load->view('common/inc/searchJobsPopup'); ?>
	<script>
    $(document).ready(function() {
        $("#queryForm").validate({
            rules: {
                userType: {
                    required: true
                },
                name: {
                    required: true,
                    minlength: 2
                },
                email: {
                    required: true,
                    email: true
                },
                query: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                userType: {
                    required: "Please select your user type."
                },
                name: {
                    required: "Please enter your name.",
                    minlength: "Your name must consist of at least 2 characters."
                },
                email: {
                    required: "Please enter your email address.",
                    email: "Please enter a valid email address."
                },
                query: {
                    required: "Please enter your query.",
                    minlength: "Your query must be at least 10 characters long."
                }
            },
            errorClass: "query-error",
            errorElement: "div"
        });
    });
</script>
	 <?php $this->load->view('common/inc/footer'); ?> 
