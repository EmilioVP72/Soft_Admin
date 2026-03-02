export interface CalculatePromotion{
    id: string;
    date: string;
    totaly_sales: number;
}

export interface TotalySalesByPromotion extends CalculatePromotion{
    acumulated_sales: number;
}