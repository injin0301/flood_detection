import { Component, OnInit } from '@angular/core';
import { FloatLabel } from 'primeng/floatlabel';
import { ButtonModule } from 'primeng/button';
import { FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';

import { FileUploadModule } from "primeng/fileupload"; 
import { HttpClientModule } from "@angular/common/http"; 
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { User } from '../../models/user';
import { CommonModule } from '@angular/common';
import { UserPut } from '../../models/userPut';

@Component({
  selector: 'app-profile-details',
  imports: [ButtonModule, FloatLabel, FormsModule, FileUploadModule, HttpClientModule, InputTextModule, CommonModule],
  providers: [],
  templateUrl: './profile-details.component.html',
  styleUrl: './profile-details.component.scss',
  standalone: true
})
export class ProfileDetailsComponent implements OnInit {
  userId: string | null = null;
  isLoading = true;
  user? : User;
  errorMessage : string = ""

  value1: string | undefined;

  value2: string | undefined;

  value3: string | undefined;

  value4: string | undefined;

  value5: string | undefined;

  value6: string | undefined;

  constructor(private route: ActivatedRoute, private apiService: ApiService, private router: Router) {}

  ngOnInit() {
    this.userId = this.route.snapshot.paramMap.get('id'); // Récupérer l'ID
    

    this.apiService.getUsers().subscribe({
      next: (response) => {
        this.isLoading = false;
        this.user = response.find(user => user.id == this.userId);

        this.value1 = this.user?.prenom;
        this.value2 = this.user?.nom;
        this.value3 = '0' + this.user?.tel;
        this.value4 = this.user?.email;
        this.value5 = this.user?.zipCode;
        this.value6 = this.user?.city;
      },
      error: (error) => {
        console.error('Erreur lors de la récupération des données', error);
      }
    });
  }

  back() {
    this.router.navigate(['/settings']);
  }

  save() {
    this.errorMessage = "";

    if(this.value3?.length != 10) {
      this.errorMessage = "Phone number is wrong"
      return;
    }
    
    if(this.value5?.length != 5) {
      this.errorMessage = "ZipCode is wrong"
      return;
    }

    let userPut : UserPut = {
      "id" : this.user!.id,
      "email": this.value4,
      "password": "1234",
      "roles": [
            "ROLE_USER"
        ],
      "nom": this.value2,
      "prenom": this.value1,
      "tel": this.value3,
      "city": this.value6,
      "zipCode": parseInt(this.value5),
      "piece": {
        "id" : 0
      }
    }

    this.isLoading = true;

    this.apiService.updateUser(this.userId, userPut).subscribe({
      next: (response) => {
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Erreur lors de la récupération des données', error);
      }
    });
  }
}
