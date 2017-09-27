import React from 'react'
import { Container, Header, Segment, Button, Grid, Card, Icon, Form, Image, Checkbox } from 'semantic-ui-react'

class HomeView extends React.Component {

  render () {
    return (
      <div>
		  	<Container className='hero-block'>
		  		<Grid columns={2}>
				    <Grid.Row>
				      <Grid.Column>
					      <Segment basic className='hero-title'>
				  				<Header size='huge'>Focus on what really matters: your book.</Header>
				  				<Header size='tiny'>Create beautiful, genre-relevant book covers without having to pay hundreds of dollars to designers.</Header>
					  		</Segment>
				      </Grid.Column>
				      <Grid.Column>
				      	<Segment floated='right' className='login-segment' basic>
					        <Card color='brown' className='login-card'>
								    <Card.Content textAlign='center'>	
					        		<Image src='/assets/images/bookforge.jpg' size='small'/>
					        		<Header as='h3'>Sign in to start creating cover designs!</Header>
								    </Card.Content>	
								    <Card.Content>	
											<Form>
											  <Form.Field>
											    <label>E-mail Address</label>
											    <input placeholder='E-mail Address' />
											  </Form.Field>
											  <Form.Field>
											    <label>Password</label>
											    <input type='password' placeholder='Password' />
											  </Form.Field>
											  <Form.Field>
										      <Checkbox label='I agree to the Terms and Conditions' />
										    </Form.Field>
											  <Button color='teal' floated='right' type='submit'>Log in</Button>
											</Form>
								    </Card.Content>
								    <Card.Content textAlign='center'>
								    	<Header as='h4'>Or login using</Header>
								    	<Button color='google plus'><Icon name='google plus square'/> Google</Button>
								    	<Button color='facebook'><Icon name='facebook square'/> Facebook</Button>
								    	<Button color='twitter'><Icon name='twitter square'/> Twitter</Button>
								    </Card.Content>
								  </Card>
				      	</Segment>
				      </Grid.Column>
				    </Grid.Row>
				  </Grid>
		  	</Container>
		  	<Container className='hero-subblock'>
	      	<Header as='h2' textAlign='center'>Our high-quality professionally-designed covers give you a head start over your competitors. Design a cover from start to finish in a matter of minutes!</Header>
	      	<br/><br/>
	      	<Image src='/assets/images/books-1.jpg'/>
		  	</Container>
		  </div>
    )
  }
}

export default HomeView
