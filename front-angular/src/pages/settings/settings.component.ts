import { Component, OnInit } from '@angular/core';
import { TableModule } from 'primeng/table';
import { HttpClientModule } from '@angular/common/http';
import { User } from '../../models/user';
import { ButtonModule } from 'primeng/button';
import { PaginatorModule, PaginatorState } from 'primeng/paginator';

interface PageEvent {
  length: number;     // Total number of items
  pageSize: number;   // Number of items per page
  pageIndex: number;  // Current page index (zero-based)
  previousPageIndex?: number;
}

@Component({
  selector: 'app-settings',
  imports: [TableModule, HttpClientModule, ButtonModule, PaginatorModule],
  providers: [],
  templateUrl: './settings.component.html',
  styleUrl: './settings.component.scss',
  standalone: true,
})
export class SettingsComponent implements OnInit {
  customers: User [] = [];
  first: number = 0;
  rows: number = 10;

  constructor() {}

  ngOnInit() {
    this.customers.push(
      { nom: 'John Doe', prenom: 'TechCorp', email : "aeaea" }
    )
      //this.customerService.getCustomersLarge().then((customers: Customer[]) => (this.customers = customers));
  }

  onPageChange(event: PaginatorState) {
    const pageEvent: PageEvent = {
      pageIndex: event.page || 0,
      pageSize: event.rows || 10, 
      length: 100 // Set a reasonable default or fetch total count dynamically
    };
    // Now use `pageEvent` safely
  }
}
