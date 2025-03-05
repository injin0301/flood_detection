import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-profile',
  imports: [ButtonModule],
  templateUrl: './profile.component.html',
  styleUrl: './profile.component.scss',
  standalone: true
})
export class ProfileComponent {
  userId = 1;

  constructor(private router: Router, private route: ActivatedRoute, private apiService: ApiService) {}

  ngOnInit() {
    this.userId = Number(this.route.snapshot.paramMap.get('id'));
  }

  toProfileDetails() {
    console.log("test")
    
    this.router.navigate(['/user-profile-details', 23]);
  }
}
