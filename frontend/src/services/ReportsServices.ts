import apiClient from '@/api/axios';

const apiBaseUrl = String(apiClient.defaults.baseURL || '').replace(/\/+$/, '');
const reportsBaseUrl = `${apiBaseUrl}/reports`;

function buildReportUrl(path: string): string {
    return `${reportsBaseUrl}/${path.replace(/^\/+/, '')}`;
}

function openReport(path: string): void {
    window.open(buildReportUrl(path), '_blank');
}

function downloadBlob(data: BlobPart, filename: string): void {
    const url = window.URL.createObjectURL(new Blob([data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    link.parentNode?.removeChild(link);
    window.URL.revokeObjectURL(url);
}

export default {
    openGeneralSalesPdf() {
        openReport('sales/general');
    },

    openGeneralSalesExcel() {
        openReport('sales/general/excel');
    },

    openStoreSalesPdf(storeId: number) {
        openReport(`sales/store/${storeId}`);
    },

    openStoreSalesExcel(storeId: number) {
        openReport(`sales/store/${storeId}/excel`);
    },

    openEmployeesPdf() {
        openReport('employees');
    },

    openEmployeesExcel() {
        openReport('employees/excel');
    },

    openStoresPdf() {
        openReport('stores');
    },

    async exportDynamicSuppliersPdf(items: any[]) {
        const response = await apiClient.post('/reports/dynamic/suppliers/pdf', { items }, { responseType: 'blob' });
        downloadBlob(response.data, 'Reporte_Proveedores.pdf');
    },

    async exportDynamicSuppliersExcel(items: any[]) {
        const response = await apiClient.post('/reports/dynamic/suppliers/excel', { items }, { responseType: 'blob' });
        downloadBlob(response.data, 'Reporte_Proveedores.xlsx');
    },

    async exportDynamicDepartmentsPdf(items: any[], storeName: string) {
        const response = await apiClient.post('/reports/dynamic/departments/pdf', { items, storeName }, { responseType: 'blob' });
        downloadBlob(response.data, 'Reporte_Departamentos.pdf');
    },

    async exportDynamicDepartmentsExcel(items: any[], storeName: string) {
        const response = await apiClient.post('/reports/dynamic/departments/excel', { items, storeName }, { responseType: 'blob' });
        downloadBlob(response.data, 'Reporte_Departamentos.xlsx');
    },

    async exportDynamicPromotionsPdf(items: any[]) {
        const response = await apiClient.post('/reports/dynamic/promotions/pdf', { items }, { responseType: 'blob' });
        downloadBlob(response.data, 'Reporte_Promociones.pdf');
    },

    async exportDynamicPromotionsExcel(items: any[]) {
        const response = await apiClient.post('/reports/dynamic/promotions/excel', { items }, { responseType: 'blob' });
        downloadBlob(response.data, 'Reporte_Promociones.xlsx');
    }
};
