import { Component, OnInit} from '@angular/core';
import { ChartModule } from 'primeng/chart';
import { Chart, registerables } from 'chart.js';
import { ApiService } from '../../services/api.service';
import { Piece } from '../../models/piece';
import { CommonModule } from '@angular/common';
import { Capteur } from '../../models/capteur';

Chart.register(...registerables);

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [ChartModule, CommonModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'] 
})
export class DashboardComponent implements OnInit {

  isLoading = true;
  numberRooms = 0;
  dataOfCaptors? = 0;
  floodNotification = 0;
  humidity = 0;
  temperature = 0;

  captorsFloodDetected? : Capteur[] = [];

  constructor(private apiService: ApiService) {}

   public config: any = {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'April'],
      datasets: [
          {
            label: 'Sales',
            data: ['467', '576'],
            backgroundColor: 'blue'
          },
          {
            label: 'Pat',
            data: ['100', '240'],
            backgroundColor: 'red'
          }
      ],
    },
    options: {
      aspectRatio: 1,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    },
  };

  chart: any;

  pieces: Piece[]= []

  ngOnInit(): void {   
    this.apiService.getPieces().subscribe({
      next: (response) => {
        this.pieces = response;
        console.log(this.pieces)
        this.isLoading = false;

        this.pieces.forEach(piece => {
          if(piece.utilisateur != null) {
            this.numberRooms = this.numberRooms + 1
          }
        });

        this.dataOfCaptors = this.pieces[0].capteur?.length
        
        this.floodNotification = this.pieces[0].capteur?.filter(capteur => capteur.inondation).length!;
        this.captorsFloodDetected = this.pieces[0].capteur?.filter(capteur => capteur.inondation);
        
        const totalHumidite = this.pieces[0].capteur?.reduce((sum, capteur) => sum + capteur.humidite!, 0);
        const totalTemperature = this.pieces[0].capteur?.reduce((sum, capteur) => sum + capteur.temperature!, 0);
        
        this.humidity = Math.round(totalHumidite! / this.dataOfCaptors!);
        this.temperature = Math.round(totalTemperature! / this.dataOfCaptors!);    
        
        //this.chart  = new Chart('MyChartBar', this.config);
      },
      error: (error) => {
        console.error('Erreur lors de la récupération des données', error);
      }
    });
  }
}
