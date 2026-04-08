import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'serviceCard',
        'vehicleCard',
        'closureEmpty',
        'closureDetails',
        'closureService',
        'closurePrice',
        'closureVehicle',
        'closureCost',
        'snapshotEmpty',
        'snapshotDetails',
        'snapshotService',
        'snapshotPrice',
        'snapshotVehicle',
        'snapshotCost',
        'snapshotDistance',
        'snapshotPackage',
    ];

    connect() {
        if (this.serviceCardTargets.length > 0) {
            this.selectServiceById(this.serviceCardTargets[0].dataset.serviceId);
        }
    }

    selectService(event) {
        const { serviceId } = event.currentTarget.dataset;
        this.selectServiceById(serviceId);
    }

    selectServiceById(serviceId) {
        this.activeServiceId = serviceId;

        let selectedServiceCard = null;
        this.serviceCardTargets.forEach((serviceCard) => {
            const isActive = serviceCard.dataset.serviceId === serviceId;
            serviceCard.classList.toggle('simulador-servicio-card--activo', isActive);
            if (isActive) {
                selectedServiceCard = serviceCard;
            }
        });

        this.vehicleCardTargets.forEach((vehicleCard) => {
            const visible = vehicleCard.dataset.serviceId === serviceId;
            vehicleCard.hidden = !visible;
            vehicleCard.classList.toggle('simulador-vehiculo-card--activo', visible && vehicleCard.dataset.recommended === '1');
        });

        if (!selectedServiceCard) {
            return;
        }

        this.closureServiceTarget.textContent = selectedServiceCard.dataset.serviceName;
        this.closurePriceTarget.textContent = selectedServiceCard.dataset.servicePrice;
        this.snapshotServiceTarget.textContent = selectedServiceCard.dataset.serviceName;
        this.snapshotPriceTarget.textContent = selectedServiceCard.dataset.servicePrice;

        this.closureEmptyTarget.hidden = false;
        this.closureDetailsTarget.hidden = true;
        this.snapshotEmptyTarget.hidden = false;
        this.snapshotDetailsTarget.hidden = true;

        const recommendedVehicle = this.vehicleCardTargets.find(
            (vehicleCard) => vehicleCard.dataset.serviceId === serviceId && vehicleCard.dataset.recommended === '1',
        ) || this.vehicleCardTargets.find((vehicleCard) => vehicleCard.dataset.serviceId === serviceId);

        if (recommendedVehicle) {
            this.closureVehicleTarget.textContent = recommendedVehicle.dataset.vehicleName;
            this.closureCostTarget.textContent = recommendedVehicle.dataset.vehicleCost;
            this.snapshotVehicleTarget.textContent = recommendedVehicle.dataset.vehicleName;
            this.snapshotCostTarget.textContent = recommendedVehicle.dataset.vehicleCost;

            this.closureEmptyTarget.hidden = true;
            this.closureDetailsTarget.hidden = false;
            this.snapshotEmptyTarget.hidden = true;
            this.snapshotDetailsTarget.hidden = false;
        }
    }
}
