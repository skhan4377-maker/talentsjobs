<?php $this->load->view('particles/top-header');?>

<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #e74c3c;
        --text-color: #34495e;
        --bg-color: #ecf0f1;
        --border-color: #bdc3c7;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background-color: var(--bg-color);
        color: var(--text-color);
    
    }

    .custom-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    h1 {
        font-size: 2.5rem;
        margin-bottom: 30px;
        color: var(--primary-color);
        text-align: center;
    }

    .custom-price-plan {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        justify-content: center;
        margin-top: 40px;
    }

    .custom-plan {
        position: relative;
        padding: 30px;
        background-color: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .custom-plan:hover {
        transform: translateY(-10px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .custom-plan-title {
        font-size: 2rem;
        color: #fff;
        background-color: var(--secondary-color);
        margin-bottom: 20px;
        padding: 15px 0;
        border-radius: 10px;
    }

    .custom-plan-price {
        font-size: 1.8rem;
        color: var(--secondary-color);
        font-weight: bold;
        margin-bottom: 15px;
    }

    .custom-plan-details {
        list-style: none;
        padding: 0;
        text-align: left;
        margin: 20px 0;
    }

    .custom-plan-details li {
        margin: 15px 0;
        color: var(--text-color);
        font-weight: 500;
        padding-left: 20px;
    }

    .custom-feature-check {
        color: #27ae60;
        font-size: 1.6rem;
    }

    .custom-buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 30px;
    }

    .custom-btn {
        width: 200px;
        padding: 15px 0;
        background-color: var(--primary-color);
        color: #fff;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-size: 1.2rem;
        transition: background-color 0.3s;
        margin-bottom: 15px;
    }

    .custom-btn:hover {
        background-color: #34495e;
    }

    .custom-btn-cart {
        width: 200px;
        padding: 15px 0;
        background-color: var(--secondary-color);
        color: #fff;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-size: 1.2rem;
        transition: background-color 0.3s;
        margin-bottom: 15px;
    }

    .custom-btn-cart:hover {
        background-color: #c0392b;
    }

    .custom-note {
        margin-top: 50px;
        font-size: 1.4rem;
        color: var(--text-color);
        background-color: #ffffff;
        border: 1px solid var(--border-color);
        padding: 30px;
        border-radius: 15px;
        text-align: left;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .custom-note ol {
        list-style-type: decimal;
        padding-left: 30px;
    }

    @media (max-width: 768px) {
        .custom-price-plan {
            grid-template-columns: 1fr;
        }

        .custom-plan {
            margin-bottom: 30px;
        }

        h1 {
            font-size: 2rem;
        }

        .custom-btn,
        .custom-btn-cart {
            width: 100%;
        }
    }

</style> 
   
<style>
    /* CSS for the circular check icon and Add to Cart button */
    .custom-plan-added::before {
        content: '✓'; /* Checkmark character */
        position: absolute;
        top: 50%; /* Vertically center the checkmark */
        left: 60px;
        transform: translateY(-50%);
        width: 20px; /* Width of the circular background */
        height: 20px; /* Height of the circular background */
        background-color: green; /* Green color for the circular background */
        border-radius: 50%; /* Makes the background circular */
        text-align: center; /* Center the checkmark within the circle */
        line-height: 20px; /* Center the checkmark vertically within the circle */
        color: white; /* Text color for the checkmark */
    }
    
    .custom-btn-cart {
        position: relative;
        padding-left: 30px; /* Add left padding for the circular background */
    }
    
	
	/* Recommended Plan Styles */
.recommended-plan {
    border: 3px solid var(--secondary-color); /* Add a border around the recommended plan */
    position: relative;
}

.recommended-plan::before {
    content: 'Recommended'; /* Badge text */
    position: absolute;
    top: -20px; /* Adjust the top position */
    left: 50%; /* Center the badge horizontally */
    transform: translateX(-50%); /* Center the badge horizontally */
    background-color: var(--secondary-color); /* Badge background color */
    color: #fff; /* Badge text color */
    padding: 5px 15px; /* Badge padding */
    border-radius: 15px; /* Badge border-radius */
    font-size: 0.9rem; /* Badge font-size */
    z-index: 1; /* Place the badge above the plan content */
}

</style>

  <style>
        <!--.custom-menu {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        .custom-menu li {
            display: inline;
            margin-right: 20px;
        }

        .custom-menu a {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
        }

        .custom-menu a:hover {
            text-decoration: underline;
        }-->
    </style>
   
</head>
<body>
<?php $this->load->view('particles/header');?>
    <section> 
        <!-- CSRF token (Here, name is 'csrf_hash_name' which is specified in $config['csrf_token_name'] in cofig.php file ) --> 
        <input type="hidden" class="txt_csrfname" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        <div class="custom-container">
        
        
    		<p style="text-align: center; font-size: 18px; margin-bottom: 20px;"><strong>Post A Job</strong></p>
            <p style="text-align: center; font-size: 18px; margin-bottom: 20px;"><strong>Quick & Easy Job Posting − Get Quality Applies</strong></p>
            
            <div id="pricing-plans-container"></div>
            
            <div class="custom-note">
                <p><strong>Important Notes:</strong></p>
                <ol>
                    <li>For quantities up to 4, job posting credits should be consumed within 30 days from the date of activation/purchase.</li>
                    <li>For quantities 5 and above, credits should be consumed within 1 year from the date of activation/purchase.</li>
                    <li>Please note that the amounts are exclusive of taxes. Taxes will be added as applicable.</li>
                    <li>Discount percentage has been rounded off to the nearest number.</li>
                </ol>
            </div>
        </div>
		
	<?php $this->load->view('particles/loginPopup');?>	
	<?php $this->load->view('particles/searchJobsPopup');?> 
    </section>
 
<script>
$(document).ready(function () {
    // Function to handle the AJAX request
    function fetchPricingPlans() {
        $.ajax({
            url: '<?= base_url("pricing/pricing/fetch_pricing_plans"); ?>',
            type: 'GET',
            dataType: 'json',
            data: { csrf_name: '<?= $this->security->get_csrf_token_name(); ?>' },
            success: function (data) {
                // Data will contain the HTML content and the updated CSRF token
                if (data.html) {
                    // Display the HTML content on your page
                    $('#pricing-plans-container').html(data.html);
                }
                // Update the CSRF token for future requests
                var updatedCSRFToken = data.csrf_token;
                $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val(updatedCSRFToken);
            },
            error: function (xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    }

    // Call the function on page load
    fetchPricingPlans();
});
</script>


<?php $this->load->view('particles/footer');?>
