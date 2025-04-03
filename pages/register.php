<div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="bg-primary py-4 px-6">
        <h2 class="text-2xl font-bold text-white">Create an Account</h2>
    </div>
    
    <form action="actions/register_process.php" method="post" class="py-6 px-8">
        <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php 
            $error_message = '';
            switch ($_GET['error']) {
                case 'empty':
                    $error_message = 'Please fill in all required fields.';
                    break;
                case 'email_exists':
                    $error_message = 'Email address is already registered. Please login or use a different email.';
                    break;
                case 'password_mismatch':
                    $error_message = 'Passwords do not match.';
                    break;
                case 'password_weak':
                    $error_message = 'Password is too weak. It should be at least 8 characters long and include letters and numbers.';
                    break;
                default:
                    $error_message = 'An error occurred. Please try again.';
            }
            echo $error_message;
            ?>
        </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="first_name" class="block text-gray-700 text-sm font-bold mb-2">First Name*</label>
                <input type="text" id="first_name" name="first_name" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="John">
            </div>
            
            <div>
                <label for="last_name" class="block text-gray-700 text-sm font-bold mb-2">Last Name*</label>
                <input type="text" id="last_name" name="last_name" required 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                    placeholder="Doe">
            </div>
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address*</label>
            <input type="email" id="email" name="email" required 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="your@email.com">
        </div>
        
        <div class="mb-4">
            <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
            <input type="tel" id="phone" name="phone" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="+31 6 12345678">
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password*</label>
            <input type="password" id="password" name="password" required 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="••••••••">
            <p class="text-xs text-gray-500 mt-1">Password should be at least 8 characters and include letters and numbers</p>
        </div>
        
        <div class="mb-6">
            <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password*</label>
            <input type="password" id="confirm_password" name="confirm_password" required 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="••••••••">
        </div>
        
        <div class="mb-6">
            <div class="flex items-start">
                <input type="checkbox" id="terms" name="terms" required class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-1">
                <label for="terms" class="ml-2 block text-sm text-gray-700">
                    I agree to the <a href="index.php?page=terms" class="text-accent hover:text-primary">Terms and Conditions</a> and <a href="index.php?page=privacy" class="text-accent hover:text-primary">Privacy Policy</a>*
                </label>
            </div>
        </div>
        
        <div class="flex flex-col gap-4">
            <button type="submit" class="bg-primary hover:bg-dark text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition-colors">
                Register
            </button>
            
            <div class="text-center text-sm">
                Already have an account? <a href="index.php?page=login" class="text-accent hover:text-primary font-semibold">Login here</a>
            </div>
        </div>
    </form>
</div> 