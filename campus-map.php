<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="content">
  <h2 class="section-title">Campus Map & Ugandan Universities</h2>
  
  <div class="row mb-5">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Our University Campus</h4>
          <p class="card-text">Find your way around our campus! Explore buildings, facilities, and key locations using the interactive map below.</p>
          
          <!-- University Campus Map -->
          <div class="map-container rounded overflow-hidden shadow-sm mb-4">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63835.25209785548!2d32.52885745!3d0.3475965!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x177dbb6e8b742bd5%3A0x982df7d5e11383e7!2sMakerere%20University!5e0!3m2!1sen!2sug!4v1690200000000!5m2!1sen!2sug" 
              width="100%" 
              height="450" 
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
          
          <div class="campus-key-locations">
            <h5 class="mb-3">Key Campus Locations:</h5>
            <div class="row">
              <div class="col-md-6">
                <ul class="list-group">
                  <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-book-reader text-primary me-3"></i>
                    <div>
                      <strong>Main Library</strong>
                      <p class="mb-0 small text-muted">Open 8:00 AM - 10:00 PM</p>
                    </div>
                  </li>
                  <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-utensils text-success me-3"></i>
                    <div>
                      <strong>Student Cafeteria</strong>
                      <p class="mb-0 small text-muted">Serves breakfast, lunch, and dinner</p>
                    </div>
                  </li>
                  <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-flask text-info me-3"></i>
                    <div>
                      <strong>Science Laboratory Complex</strong>
                      <p class="mb-0 small text-muted">Building A, Floor 2-4</p>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul class="list-group">
                  <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-basketball-ball text-warning me-3"></i>
                    <div>
                      <strong>Sports Complex</strong>
                      <p class="mb-0 small text-muted">Gym, pool, and courts available</p>
                    </div>
                  </li>
                  <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-first-aid text-danger me-3"></i>
                    <div>
                      <strong>Health Center</strong>
                      <p class="mb-0 small text-muted">Open 24/7 for emergencies</p>
                    </div>
                  </li>
                  <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-desktop text-secondary me-3"></i>
                    <div>
                      <strong>Computer Lab</strong>
                      <p class="mb-0 small text-muted">Open 8:00 AM - 9:00 PM</p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"><i class="fas fa-graduation-cap me-2 text-primary"></i>Universities in Uganda</h4>
          <p class="card-text">Uganda has several prestigious universities offering diverse academic programs. Here are some of the major universities:</p>
          
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>University</th>
                  <th>Location</th>
                  <th>Established</th>
                  <th>Type</th>
                  <th>Website</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Makerere University</strong></td>
                  <td>Kampala</td>
                  <td>1922</td>
                  <td>Public</td>
                  <td><a href="https://www.mak.ac.ug" target="_blank">mak.ac.ug</a></td>
                </tr>
                <tr>
                  <td><strong>Kyambogo University</strong></td>
                  <td>Kampala</td>
                  <td>2003</td>
                  <td>Public</td>
                  <td><a href="https://www.kyu.ac.ug" target="_blank">kyu.ac.ug</a></td>
                </tr>
                <tr>
                  <td><strong>Uganda Christian University</strong></td>
                  <td>Mukono</td>
                  <td>1997</td>
                  <td>Private</td>
                  <td><a href="https://www.ucu.ac.ug" target="_blank">ucu.ac.ug</a></td>
                </tr>
                <tr>
                  <td><strong>Uganda Martyrs University</strong></td>
                  <td>Nkozi</td>
                  <td>1993</td>
                  <td>Private</td>
                  <td><a href="https://www.umu.ac.ug" target="_blank">umu.ac.ug</a></td>
                </tr>
                <tr>
                  <td><strong>Busitema University</strong></td>
                  <td>Busia</td>
                  <td>2007</td>
                  <td>Public</td>
                  <td><a href="https://www.busitema.ac.ug" target="_blank">busitema.ac.ug</a></td>
                </tr>
                <tr>
                  <td><strong>Mbarara University of Science & Technology</strong></td>
                  <td>Mbarara</td>
                  <td>1989</td>
                  <td>Public</td>
                  <td><a href="https://www.must.ac.ug" target="_blank">must.ac.ug</a></td>
                </tr>
                <tr>
                  <td><strong>Gulu University</strong></td>
                  <td>Gulu</td>
                  <td>2002</td>
                  <td>Public</td>
                  <td><a href="https://www.gu.ac.ug" target="_blank">gu.ac.ug</a></td>
                </tr>
                <tr>
                  <td><strong>Islamic University in Uganda</strong></td>
                  <td>Mbale</td>
                  <td>1988</td>
                  <td>Private</td>
                  <td><a href="https://www.iuiu.ac.ug" target="_blank">iuiu.ac.ug</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"><i class="fas fa-map me-2 text-primary"></i>Uganda University Locations Map</h4>
          <p class="card-text">Explore the geographical distribution of universities across Uganda. This map shows the locations of major universities with markers:</p>
          
          <div class="map-container rounded overflow-hidden shadow-sm mb-4">
            <iframe 
              src="https://www.google.com/maps/d/embed?mid=1eT6Qp8f7U4fF9pJ4K4l6J4K4l6J4K4l6&ehbc=2E312F" 
              width="100%" 
              height="450" 
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
          
          <div class="university-map-legend">
            <h5 class="mb-3">University Map Legend:</h5>
            <div class="row">
              <div class="col-md-3 col-6 mb-2">
                <i class="fas fa-map-marker-alt text-danger me-2"></i> Makerere University
              </div>
              <div class="col-md-3 col-6 mb-2">
                <i class="fas fa-map-marker-alt text-primary me-2"></i> Kyambogo University
              </div>
              <div class="col-md-3 col-6 mb-2">
                <i class="fas fa-map-marker-alt text-success me-2"></i> Uganda Christian University
              </div>
              <div class="col-md-3 col-6 mb-2">
                <i class="fas fa-map-marker-alt text-warning me-2"></i> Uganda Martyrs University
              </div>
              <div class="col-md-3 col-6 mb-2">
                <i class="fas fa-map-marker-alt text-info me-2"></i> Busitema University
              </div>
              <div class="col-md-3 col-6 mb-2">
                <i class="fas fa-map-marker-alt text-secondary me-2"></i> Mbarara University
              </div>
              <div class="col-md-3 col-6 mb-2">
                <i class="fas fa-map-marker-alt text-dark me-2"></i> Gulu University
              </div>
              <div class="col-md-3 col-6 mb-2">
                <i class="fas fa-map-marker-alt text-purple me-2"></i> Islamic University
              </div>
            </div>
          </div>
          
          <div class="mt-4">
            <h5>Quick Facts About Ugandan Higher Education:</h5>
            <div class="row mt-3">
              <div class="col-md-3 col-6 text-center mb-3">
                <div class="bg-light p-3 rounded">
                  <h3 class="text-primary mb-0">50+</h3>
                  <small>Universities</small>
                </div>
              </div>
              <div class="col-md-3 col-6 text-center mb-3">
                <div class="bg-light p-3 rounded">
                  <h3 class="text-success mb-0">200K+</h3>
                  <small>University Students</small>
                </div>
              </div>
              <div class="col-md-3 col-6 text-center mb-3">
                <div class="bg-light p-3 rounded">
                  <h3 class="text-info mb-0">100+</h3>
                  <small>Study Programs</small>
                </div>
              </div>
              <div class="col-md-3 col-6 text-center mb-3">
                <div class="bg-light p-3 rounded">
                  <h3 class="text-warning mb-0">40%</h3>
                  <small>Female Enrollment</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.section-title {
  position: relative;
  padding-bottom: 15px;
  margin-bottom: 30px;
  font-weight: 700;
}

.section-title:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 4px;
  background: #f39c12;
  border-radius: 2px;
}

.map-container {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
}

.card {
  border: none;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
  margin-bottom: 20px;
}

.card-title {
  color: #2c3e50;
  font-weight: 600;
}

.list-group-item {
  border: 1px solid rgba(0,0,0,0.125);
  border-radius: 0.375rem;
  margin-bottom: 10px;
}

.table th {
  background-color: #2c3e50;
  color: white;
}

.text-purple {
  color: #6f42c1;
}
</style>

<?php include 'footer.php'; ?>