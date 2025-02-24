import { Component } from '@angular/core';

@Component({
  selector: 'app-login',
  imports: [],
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss'
})
export class LoginComponent {
  import { Component, OnInit } from '@angular/core';
  import { FormBuilder, FormGroup, Validators } from '@angular/forms';
  import { AuthService } from 'src/app/services/auth.service';
  import { Router } from '@angular/router';
  
  @Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.scss']
  })
  export class LoginComponent implements OnInit {
    loginForm!: FormGroup;
    errorMessage: string = '';
  
    constructor(
      private fb: FormBuilder,
      private authService: AuthService,
      private router: Router
    ) {}
  
    ngOnInit(): void {
      this.loginForm = this.fb.group({
        username: ['', Validators.required],
        password: ['', Validators.required]
      });
    }
  
    onSubmit(): void {
      if (this.loginForm.valid) {
        const credentials = this.loginForm.value;
        this.authService.login(credentials).subscribe(
          (response) => {
            // Stocker le token JWT et rediriger l'utilisateur vers le dashboard
            this.authService.setToken(response.token);
            this.router.navigate(['/dashboard']);
          },
          (error) => {
            this.errorMessage = 'Nom d\'utilisateur ou mot de passe incorrect';
          }
        );
      }
    }
  }  
}
