// 'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{12,}$/',
    // 'title' => 'Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'
    // script pour afficher les caractere necessaire pour le mot de passe en rouge et vert quand il est bon

    let passwordStrength = document.getElementById('password-strength');
    let passwordInput = document.getElementById('password');
    let button = document.getElementById('button');
    button.disabled = true;

    passwordInput.addEventListener('keyup', function () {
        let password = passwordInput.value;
        let strength = 0;
        if (password.match(/[a-z]+/)) {
            strength += 1;
        }
        if (password.match(/[A-Z]+/)) {
            strength += 1;
        }
        if (password.match(/[0-9]+/)) {
            strength += 1;
        }
        if (password.match(/\W+/)) {
            strength += 1;
        }
        if (password.length >= 12) {
            strength += 1;
        }
        switch (strength) {
            case 0:
                passwordStrength.innerHTML = '';
                break;
            case 1:
                passwordStrength.innerHTML = '<p class="text-danger mt-2"><small>Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial</small></p>';
                break;
            case 2:
                passwordStrength.innerHTML = '<p class="text-danger mt-2"><small>Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial</small></p>';
                break;
            case 3:
                passwordStrength.innerHTML = '<p class="text-warning mt-2"><small>Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial</small></p>';
                break;
            case 4:
                passwordStrength.innerHTML = '<p class="text-warning mt-2"><small>Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial</small></p>';
                break
            case 5:
                passwordStrength.innerHTML = '<p class="text-success mt-2"><small>Le mot de passe est valide</small></p>';
                button.disabled = false;
                break;
        }
    });