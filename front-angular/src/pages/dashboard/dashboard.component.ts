import { Component, OnInit} from '@angular/core';
import { ChartModule } from 'primeng/chart';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [ChartModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'] 
})
export class DashboardComponent implements OnInit {
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

  /*public config2: any = {
    type: 'pie',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'April'],
      datasets: [
          {
            label: 'Sales',
            data: ['467', '576'],
            backgroundColor: 'purple'
          },
          {
            label: 'Pat',
            data: ['100', '240'],
            backgroundColor: 'pink'
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
  };*/

  chart: any;
  //chart2: any;

  ngOnInit(): void {
    this.chart  = new Chart('MyChartBar', this.config);
    //this.chart2  = new Chart('MyChartPie', this.config2);
  }
}
