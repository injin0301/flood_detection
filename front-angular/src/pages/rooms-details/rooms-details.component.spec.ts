import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RoomsDetailsComponent } from './rooms-details.component';

describe('RoomsDetailsComponent', () => {
  let component: RoomsDetailsComponent;
  let fixture: ComponentFixture<RoomsDetailsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RoomsDetailsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RoomsDetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
