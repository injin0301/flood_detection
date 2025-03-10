import { Component, OnInit } from '@angular/core';
import { TableModule } from 'primeng/table';
import { HttpClientModule } from '@angular/common/http';
import { User } from '../../models/user';
import { ButtonModule } from 'primeng/button';
import { PaginatorModule, PaginatorState } from 'primeng/paginator';
import { ApiService } from '../../services/api.service';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

interface PageEvent {
  length: number;     // Total number of items
  pageSize: number;   // Number of items per page
  pageIndex: number;  // Current page index (zero-based)
  previousPageIndex?: number;
}

@Component({
  selector: 'app-settings',
  imports: [TableModule, HttpClientModule, ButtonModule, PaginatorModule, CommonModule],
  providers: [],
  templateUrl: './settings.component.html',
  styleUrl: './settings.component.scss',
  standalone: true,
})
export class SettingsComponent implements OnInit {
  isLoading = true;
  customers: User [] = [];
  first: number = 0;
  rows: number = 10;

  constructor(private apiService: ApiService, private router: Router) {}

  ngOnInit() {
    this.apiService.getUsers().subscribe({
      next: (response) => {
        this.isLoading = false;
        response.forEach(user => {
          this.customers.push(user);
        });
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

  editUser(customerId: User) {
    this.router.navigate(['/user-profile-details', customerId]);
  }
}
