/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.codehaus.jackson.annotate.JsonIgnore;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "user")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "User.findAll", query = "SELECT u FROM User u"),
    @NamedQuery(name = "User.findByUserId", query = "SELECT u FROM User u WHERE u.userId = :userId"),
    @NamedQuery(name = "User.findByFname", query = "SELECT u FROM User u WHERE u.fname = :fname"),
    @NamedQuery(name = "User.findByLname", query = "SELECT u FROM User u WHERE u.lname = :lname"),
    @NamedQuery(name = "User.findByUsername", query = "SELECT u FROM User u WHERE u.username = :username"),
    @NamedQuery(name = "User.findByPassword", query = "SELECT u FROM User u WHERE u.password = :password"),
    @NamedQuery(name = "User.findByAffiliation", query = "SELECT u FROM User u WHERE u.affiliation = :affiliation"),
    @NamedQuery(name = "User.findByUserRole", query = "SELECT u FROM User u WHERE u.userRole = :userRole")})

@NamedNativeQueries({
    @NamedNativeQuery(name = "nativeSQL.login", query = "SELECT u.user_id, u.fname, u.lname, u.affiliation FROM `user` u WHERE u.`username` LIKE ?1 AND password = PASSWORD(?2) ", resultClass = User.class)
})
public class User implements Serializable {
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "userId")
    private Collection<PrefQT> prefQTCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "userId")
    private Collection<PrefQL> prefQLCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "userId")
    private Collection<Notification> notificationCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "userId")
    private Collection<Liveinterest> liveinterestCollection;
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    //@NotNull
    @Column(name = "user_id")
    private Long userId;
    @Basic(optional = false)
    //@NotNull
    @Size(min = 1, max = 30)
    @Column(name = "fname")
    private String fname;
    @Size(max = 30)
    @Column(name = "lname")
    private String lname;
    @Basic(optional = false)
    //@NotNull
    @Size(min = 1, max = 20)
    @Column(name = "username")
    private String username;
    @Basic(optional = false)
    //@NotNull
    @Size(min = 1, max = 40)
    @Column(name = "password")
    private String password;
    @Size(max = 45)
    @Column(name = "affiliation")
    private String affiliation;
    @Basic(optional = false)
    //@NotNull
    @Size(min = 1, max = 20)
    @Column(name = "user_role")
    private String userRole;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "userTarId",
            fetch = FetchType.LAZY)
    private Collection<AnnoForUser> annoForUserCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "userId",
            fetch = FetchType.LAZY)
    private Collection<Annotation> annotationCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "userSrcId",
            fetch = FetchType.LAZY)
    private Collection<UserBelongGroup> userBelongGroupCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "userId",
            fetch = FetchType.LAZY)
    private Collection<SetInfo> setInfoCollection;

    public User() {
    }

    public User(Long userId) {
        this.userId = userId;
    }

    public User(Long userId, String fname, String username, String password, String userRole) {
        this.userId = userId;
        this.fname = fname;
        this.username = username;
        this.password = password;
        this.userRole = userRole;
    }

    public Long getUserId() {
        return userId;
    }

    public void setUserId(Long userId) {
        this.userId = userId;
    }

    public String getFname() {
        return fname;
    }

    public void setFname(String fname) {
        this.fname = fname;
    }

    public String getLname() {
        return lname;
    }

    public void setLname(String lname) {
        this.lname = lname;
    }

    public String getUsername() {
        return username;
    }

    public void setUsername(String username) {
        this.username = username;
    }
    
    @XmlTransient
    @JsonIgnore
    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public String getAffiliation() {
        return affiliation;
    }

    public void setAffiliation(String affiliation) {
        this.affiliation = affiliation;
    }

    public String getUserRole() {
        return userRole;
    }

    public void setUserRole(String userRole) {
        this.userRole = userRole;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<AnnoForUser> getAnnoForUserCollection() {
        return annoForUserCollection;
    }

    public void setAnnoForUserCollection(Collection<AnnoForUser> annoForUserCollection) {
        this.annoForUserCollection = annoForUserCollection;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<Annotation> getAnnotationCollection() {
        return annotationCollection;
    }

    public void setAnnotationCollection(Collection<Annotation> annotationCollection) {
        this.annotationCollection = annotationCollection;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<UserBelongGroup> getUserBelongGroupCollection() {
        return userBelongGroupCollection;
    }

    public void setUserBelongGroupCollection(Collection<UserBelongGroup> userBelongGroupCollection) {
        this.userBelongGroupCollection = userBelongGroupCollection;
    }

   /* @XmlTransient
    @JsonIgnore
    public Collection<PrefForUser> getPrefForUserCollection() {
        return prefForUserCollection;
    }

    public void setPrefForUserCollection(Collection<PrefForUser> prefForUserCollection) {
        this.prefForUserCollection = prefForUserCollection;
    }
    * 
    */

    @XmlTransient
    @JsonIgnore
    public Collection<SetInfo> getSetInfoCollection() {
        return setInfoCollection;
    }

    public void setSetInfoCollection(Collection<SetInfo> setInfoCollection) {
        this.setInfoCollection = setInfoCollection;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (userId != null ? userId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof User)) {
            return false;
        }
        User other = (User) object;
        if ((this.userId == null && other.userId != null) || (this.userId != null && !this.userId.equals(other.userId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.User[ userId=" + userId + " ]";
    }

    @XmlTransient
    @JsonIgnore
    public Collection<Notification> getNotificationCollection() {
        return notificationCollection;
    }

    public void setNotificationCollection(Collection<Notification> notificationCollection) {
        this.notificationCollection = notificationCollection;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<Liveinterest> getLiveinterestCollection() {
        return liveinterestCollection;
    }

    public void setLiveinterestCollection(Collection<Liveinterest> liveinterestCollection) {
        this.liveinterestCollection = liveinterestCollection;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<PrefQL> getPrefQLCollection() {
        return prefQLCollection;
    }

    public void setPrefQLCollection(Collection<PrefQL> prefQLCollection) {
        this.prefQLCollection = prefQLCollection;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<PrefQT> getPrefQTCollection() {
        return prefQTCollection;
    }

    public void setPrefQTCollection(Collection<PrefQT> prefQTCollection) {
        this.prefQTCollection = prefQTCollection;
    }
    
}
