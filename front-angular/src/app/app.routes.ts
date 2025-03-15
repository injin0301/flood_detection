import { Routes } from '@angular/router';
import { DashboardComponent } from '../pages/dashboard/dashboard.component';
import { SettingsComponent } from '../pages/settings/settings.component';
import { ProfileComponent } from '../pages/profile/profile.component';
import { ProfileDetailsComponent } from '../pages/profile-details/profile-details.component';
import { LandingPageComponent } from '../pages/landing-page/landing-page.component';
import { LoginComponent } from '../pages/login/login.component';
import { RegisterComponent } from '../pages/register/register.component';
import { RoomsComponent } from '../pages/rooms/rooms.component';
import { RoomsDetailsComponent } from '../pages/rooms-details/rooms-details.component';

export const routes: Routes = [
    {path: '', component: LandingPageComponent},
    {path: 'dashboard', component: DashboardComponent},
    {path: 'user-profile', component: ProfileComponent},
    {path: 'user-profile-details/:id', component: ProfileDetailsComponent},
    {path: 'room-details/:id', component: RoomsDetailsComponent},
    {path: 'settings', component: SettingsComponent},
    {path: 'rooms', component: RoomsComponent},
    {path: 'signin', component: LoginComponent},
    {path: 'signup', component: RegisterComponent}
];
