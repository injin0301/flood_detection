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
import { Piece } from '../../models/piece';
import { RoomPut } from '../../models/roomPut';

@Component({
  selector: 'app-rooms-details',
  imports: [ButtonModule, FloatLabel, FormsModule, FileUploadModule, HttpClientModule, InputTextModule, CommonModule],
  templateUrl: './rooms-details.component.html',
  styleUrl: './rooms-details.component.scss'
})
export class RoomsDetailsComponent implements OnInit {
  roomId: string | null = null;
  isLoading = true;
  room? : Piece;
  errorMessage : string = ""

  value1: string | undefined;

  value2: string | undefined;

  constructor(private route: ActivatedRoute, private apiService: ApiService, private router: Router) {}

  ngOnInit() {
    this.roomId = this.route.snapshot.paramMap.get('id'); // Récupérer l'ID
    

    this.apiService.getPiece(this.roomId).subscribe({
      next: (response) => {
        this.isLoading = false;
        this.room = response;

        this.value1 = this.room?.nom
        this.value2 = this.room?.description

        console.log(this.room)
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

    if(this.value1 == "") {
      this.errorMessage = "Room name is necessary"
    }

    if(this.value2 == "") {
      this.errorMessage = "Description is necessary"
    }

    let roomPut : RoomPut = {
      "idUtilisateur" : this.room!.utilisateur!.id,
      "nom": this.value1,
      "description": this.value2
    }

    this.isLoading = true;

    this.apiService.updatePiece(this.roomId, roomPut).subscribe({
      next: (response) => {
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Erreur lors de la récupération des données', error);
      }
    });
  }
}
