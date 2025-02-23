import { Routes } from '@angular/router';
import { DashboardComponent } from '../pages/dashboard/dashboard.component';
import { SettingsComponent } from '../pages/settings/settings.component';
import { ProfileComponent } from '../pages/profile/profile.component';
import { ProfileDetailsComponent } from '../pages/profile-details/profile-details.component';
import { LandingPageComponent } from '../pages/landing-page/landing-page.component';
import { LoginComponent } from '../pages/login/login.component';
import { RegisterComponent } from '../pages/register/register.component';

export const routes: Routes = [
    {path: '', component: LandingPageComponent},
    {path: 'dashboard', component: DashboardComponent},
    {path: 'user-profile', component: ProfileComponent},
    {path: 'user-profile-details/:id', component: ProfileDetailsComponent},
    {path: 'settings', component: SettingsComponent},
    {path: 'signin', component: LoginComponent},
    {path: 'signup', component: RegisterComponent}
];
