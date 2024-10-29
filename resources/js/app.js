import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import Layout from './Layouts/Layout.vue';
import { Link } from "@inertiajs/vue3";
import FlashSuccess from './Partial/FlashSuccess.vue';
import FlashFail from './Partial/FlashFail.vue';
import Confirmation from './Partial/Confirmation.vue';
import { OhVueIcon, addIcons } from "oh-vue-icons";

// Import for Reports
import CustomerStatus from './Data/Customer/CustomerStatus.vue';
import CollectionLocation from './Data/Collection/CollectionLocation.vue';
import CollectionCrosstab from './Data/Collection/CollectionSummaryCrosstab.vue';
import CollectionPaymentCenter from './Data/Collection/CollectionPaymentCenter.vue';
import MeterReading from './Data/MeterReading/MeterReading.vue';

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement,
  LineElement,
  PointElement,
  RadialLinearScale
} from 'chart.js'
import { Bar, Doughnut, Line, PolarArea } from 'vue-chartjs'
import { FaFlag, RiZhihuFill, FaBars, MdLogout, BiWatch, BiGearFill, RiUserAddFill, RiErrorWarningLine, IoPersonRemoveSharp, BiCheckCircleFill, BiXCircleFill, FaRegularChartBar, BiTable, FaChartPie, BiPlusCircleFill, IoRemoveCircle, BiTrashFill, BiCaretRightSquareFill, BiCaretLeftSquareFill, OiXCircleFill, FaWindowMinimize, RiGroupFill, FaUserEdit, CoPlaylistAdd, BiArrowsFullscreen   } from "oh-vue-icons/icons";

ChartJS.register(CategoryScale, LinearScale, BarElement, ArcElement, LineElement, PointElement, RadialLinearScale, Title, Tooltip, Legend)
addIcons(FaFlag, RiZhihuFill, FaBars, MdLogout, BiWatch, BiGearFill, RiUserAddFill, RiErrorWarningLine, IoPersonRemoveSharp, BiCheckCircleFill, BiXCircleFill, FaRegularChartBar, BiTable, FaChartPie, BiPlusCircleFill, IoRemoveCircle, BiTrashFill, BiCaretRightSquareFill, BiCaretLeftSquareFill, OiXCircleFill, FaWindowMinimize, RiGroupFill,FaUserEdit, CoPlaylistAdd, BiArrowsFullscreen   );

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || Layout;
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .component("Link", Link)
      .component("Bar", Bar)
      .component("Doughnut", Doughnut)
      .component("Line", Line)
      .component("PolarArea", PolarArea)
      .component("FlashSuccess", FlashSuccess)
      .component("FlashFail", FlashFail)
      .component("Confirmation", Confirmation)
      .component("v-icon", OhVueIcon)
      // Reports
      .component("CustomerStatus", CustomerStatus)
      .component("CollectionLocation", CollectionLocation)
      .component("CollectionCrosstab", CollectionCrosstab)
      .component("CollectionPaymentCenter", CollectionPaymentCenter)
      .component("MeterReading", MeterReading)
      .mount(el)
  },
})
