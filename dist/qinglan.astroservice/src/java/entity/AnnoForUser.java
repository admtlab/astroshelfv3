/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.xml.bind.annotation.XmlRootElement;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "anno_for_user")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "AnnoForUser.findAll", query = "SELECT a FROM AnnoForUser a"),
    @NamedQuery(name = "AnnoForUser.findByAnnoForUserId", query = "SELECT a FROM AnnoForUser a WHERE a.annoForUserId = :annoForUserId")})
public class AnnoForUser implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "anno_for_user_id")
    private Long annoForUserId;
    @JoinColumn(name = "anno_src_id", referencedColumnName = "anno_id")
    @ManyToOne(optional = false)
    private Annotation annoSrcId;
    @JoinColumn(name = "user_tar_id", referencedColumnName = "user_id")
    @ManyToOne(optional = false)
    private User userTarId;

    public AnnoForUser() {
    }

    public AnnoForUser(Long annoForUserId) {
        this.annoForUserId = annoForUserId;
    }

    public Long getAnnoForUserId() {
        return annoForUserId;
    }

    public void setAnnoForUserId(Long annoForUserId) {
        this.annoForUserId = annoForUserId;
    }

    public Annotation getAnnoSrcId() {
        return annoSrcId;
    }

    public void setAnnoSrcId(Annotation annoSrcId) {
        this.annoSrcId = annoSrcId;
    }

    public User getUserTarId() {
        return userTarId;
    }

    public void setUserTarId(User userTarId) {
        this.userTarId = userTarId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (annoForUserId != null ? annoForUserId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof AnnoForUser)) {
            return false;
        }
        AnnoForUser other = (AnnoForUser) object;
        if ((this.annoForUserId == null && other.annoForUserId != null) || (this.annoForUserId != null && !this.annoForUserId.equals(other.annoForUserId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.AnnoForUser[ annoForUserId=" + annoForUserId + " ]";
    }
    
}
