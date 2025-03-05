import { Component, OnInit } from '@angular/core';
import { TableModule } from 'primeng/table';
import { HttpClientModule } from '@angular/common/http';
import { User } from '../../models/user';
import { ButtonModule } from 'primeng/button';
import { PaginatorModule, PaginatorState } from 'primeng/paginator';
import { ApiService } from '../../services/api.service';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { Piece } from '../../models/piece';

interface PageEvent {
  length: number;     // Total number of items
  pageSize: number;   // Number of items per page
  pageIndex: number;  // Current page index (zero-based)
  previousPageIndex?: number;
}

@Component({
  selector: 'app-rooms',
  imports: [TableModule, HttpClientModule, ButtonModule, PaginatorModule, CommonModule],
  templateUrl: './rooms.component.html',
  styleUrl: './rooms.component.scss'
})
export class RoomsComponent implements OnInit {
  isLoading = true;
  rooms: Piece [] = [];
  first: number = 0;
  rows: number = 10;

  constructor(private apiService: ApiService, private router: Router) {}

  ngOnInit() {
    this.apiService.getPieces().subscribe({
      next: (response) => {
        this.isLoading = false;
        response.forEach(room => {
          if(room.utilisateur != null) {
            this.rooms.push(room);
          }  
        });
        console.log(this.rooms)
      },
      error: (error) => {
        console.error('Erreur lors de la récupération des données', error);
      }
    });
  }

  onPageChange(event: PaginatorState) {
    const pageEvent: PageEvent = {
      pageIndex: event.page || 0,
      pageSize: event.rows || 10, 
      length: 100 // Set a reasonable default or fetch total count dynamically
    };
    // Now use `pageEvent` safely
  }

  editRoom(roomId: Piece) {
    this.router.navigate(['/room-details', roomId]);
  }
}
