import { Component } from '@angular/core';
import { FloatLabel } from 'primeng/floatlabel';
import { ButtonModule } from 'primeng/button';
import { FormsModule } from '@angular/forms';
import { InputTextModule } from 'primeng/inputtext';

import { FileUploadModule } from "primeng/fileupload"; 
import { HttpClientModule } from "@angular/common/http"; 

@Component({
  selector: 'app-profile-details',
  imports: [ButtonModule, FloatLabel, FormsModule, FileUploadModule, HttpClientModule, InputTextModule],
  providers: [],
  templateUrl: './profile-details.component.html',
  styleUrl: './profile-details.component.scss',
  standalone: true
})
export class ProfileDetailsComponent {
  value1: string | undefined;

  value2: string | undefined;

  value3: string | undefined;

  value4: string | undefined;

  constructor() {}

}
