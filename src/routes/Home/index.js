import HomeView from './components/HomeView'
import LandingHeader from 'components/Headers/LandingHeader'
import LandingFooter from 'components/Footers/LandingFooter'

// Sync route definition
export default {
  components: {
  	header: LandingHeader,
  	content: HomeView,
  	footer: LandingFooter
  }
}
