import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { HttpErrorResponse } from '@angular/common/http';
import { AuthService } from '../../app/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  loginForm!: FormGroup;
  errorMessage: string = '';
  returnUrl: string = '/dashboard';

  constructor(
    private fb: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required]
    });
    this.route.queryParams.subscribe(params => {
      this.returnUrl = params['returnUrl'] || '/dashboard';
    });
  }

  onSubmit(): void {
    if (this.loginForm.valid) {
      const credentials = this.loginForm.value;
      console.log('Données envoyées au login :', credentials);
  
      this.authService.login(credentials).subscribe({
        next: (response: { token: string }) => {
          console.log('Connexion réussie ! Token reçu :', response.token);
          localStorage.setItem('token', response.token);
          this.router.navigate([this.returnUrl]);
        },
        error: (error: HttpErrorResponse) => {
          console.error('Erreur lors de la connexion :', error);
          this.errorMessage = "Email ou mot de passe incorrect";
        }
      });
    } else {
      this.errorMessage = "Veuillez remplir tous les champs.";
    }
  }
}
